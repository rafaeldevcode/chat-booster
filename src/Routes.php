<?php

namespace ChatBooster;

use ChatBooster\Models\CbLead;
use ChatBooster\Models\CbMessage;
use ChatBooster\Models\CbSetting;
use ChatBooster\Requests\Requests;
use ChatBooster\Seeders\Seed;

class Routes extends Requests
{
    public function __construct()
    {
        parent::__construct();

        if(!Setting::hasFileSettings() && $this->method !== 'createFileSetting'):
            $this->fileSettingsNotDefined();
        elseif(!empty(Setting::hasMigrations()) && $this->method !== 'runMigrations'):
            $this->notMigrated();
        elseif(!$this->verifyMethod()):
            $this->methodNotSpecified();
        elseif(method_exists($this, $this->method)):
            $this->{$this->method}();
        else:
            $this->methodNotExists();
        endif;

        echo json_encode([
            'code' => $this->code,
            'status' => $this->status,
            'message' => $this->message,
            'method' => $this->method,
            'data' => $this->data,
        ]);
    }

    private function runMigrations(): void
    {
        $migrations = Setting::hasMigrations();

        foreach($migrations as $migrate):
            $prefix = substr($migrate, 0, 5);

            $migrate = str_replace($prefix, '', $migrate);
            $migrate = ucwords(str_replace('_', ' ', $migrate));
            $migrate = str_replace(' ', '', $migrate);

            require __DIR__."/Migrations/{$prefix}{$migrate}.php";

            $class = "ChatBooster\\Migrations\\{$migrate}";
            $class = new $class;

            $class->init();
        endforeach;

        Seed::init();

        if(empty($migrations)):
            $code = 200;
            $status = true;
            $message = 'The migrations have already been executed';
        else:
            $code = 201;
            $status = true;
            $message = 'Migrations executed successfully!';
        endif;

        $this->setCode($code)
            ->setStatus($status)
            ->setMessage($message);
    }

    private function createFileSetting(): void
    {
        if(!Setting::hasFileSettings()):
            $script = <<<EOT
            <?php
                
                define('DB_HOST', '{$this->requests->db_host}');
                define('DB_USERNAME', '{$this->requests->db_user}');
                define('DB_PASSWORD', '{$this->requests->db_password}');
                define('DB_DATABASE_NAME', '{$this->requests->db_name}');
                define('DB_PORT', '{$this->requests->db_port}');
            EOT;

            $file = fopen(__DIR__.'/../../../../chatbooster.settings.php', 'w');

            fwrite($file, $script);

            fclose($file);

            $this->setCode(201)
                ->setStatus(true)
                ->setMessage('Configuration file created successfully!');
        else:
            $this->setCode(200)
                ->setStatus(true)
                ->setMessage('Settings file already exists settings file already exists!');
        endif;
    }

    private function saveSettings()
    {
        $setting = new CbSetting();
        $setting = $setting->find($this->requests->id)
            ->update([
                'name' => $this->requests->name,
                'avatar' => $this->requests->avatar,
                'chat' => $this->requests->chat,
            ]);

        $this->setCode(201)
            ->setStatus(true)
            ->setMessage('Settings saved successfully!');
    }

    private function resetDatabaseSettings(): void
    {
        if(file_exists(__DIR__.'/../../../../chatbooster.settings.php')):
            unlink(__DIR__.'/../../../../chatbooster.settings.php');

            $this->setCode(201)
                ->setStatus(true)
                ->setMessage('Settings file removed successfully!');
        else:
            $this->setCode(404)
                ->setStatus(false)
                ->setMessage('Settings file does not exist!');
        endif;
    }

    private function loadMore(): void
    {
        $leads = new CbLead();
        $leads = $leads->paginate(15, 'updated_at');
        $data = [
            'next' => $leads->next,
            'leads' => $leads->data,
        ];

        $this->setCode(200)
            ->setStatus(true)
            ->setData($data);
    }

    private function showLead(): void
    {
        $this->setCode(200)
            ->setStatus(true)
            ->setData($this->getMessages());  
    }

    private function saveChat(): void
    {
        if($this->requests->from == '0'):
            $lead = new CbLead();
            $lead = $lead->create([
                'name' => 'Novo Usuário'
            ]);
        endif;

        if($this->requests->to == '0'):
            $lead = new CbLead();
            $lead = $lead->create([
                'name' => 'Novo Usuário'
            ]);
        endif;

        $message = new CbMessage();
        $message->create([
            'message' => $this->requests->message,
            'from' => $this->requests->from == '0' ? $lead->id : $this->requests->from,
            'to' => $this->requests->to == '0' ? $lead->id : $this->requests->to,
        ]);

        $this->setCode(200)
            ->setStatus(true)
            ->setData([
                'message' => $this->requests->message,
                'from' => $this->requests->from == '0' ? $lead->id : $this->requests->from,
                'to' => $this->requests->to == '0' ? $lead->id : $this->requests->to,
            ]);
    }

    private function getMessages(): array
    {
        $lead = new CbLead();
        $messages = new CbMessage();

        $lead = $lead->find($this->requests->to);

        $from = $messages->where('to', '=', $this->requests->to)
            ->where('from', '=', $this->requests->from)
            ->get();
        $from = is_null($from) ? [] : json_decode(json_encode($from), true);

        $to = $messages->where('to', '=', $this->requests->from)
            ->where('from', '=', $this->requests->to)
            ->get();
        $to = is_null($to) ? [] : json_decode(json_encode($to), true);

        $result = array_merge($from, $to);

        usort($result, 'ChatBooster\Setting::sortByCreatedAt');

        return [
            'lead' => $lead->data,
            'messages' => $result,
        ];
    }
}