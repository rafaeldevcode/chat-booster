<section data-chat="list-leads" data-chat-menu-content="leads">
    <?php foreach(self::$leads->data as $lead): ?>
        <button data-chat-leads="<?php echo $lead->id ?>" type="button" title="<?php echo $lead->name ?>" class="chat-leads">
            <div class="chat-leads-user-avatar">
                <img src="<?php echo $lead->avatar ?>" alt="<?php echo $lead->name ?>">
            </div>

            <p><?php echo $lead->name?></p>
        </button>
    <?php endforeach ?>
</section>

<?php if(isset(self::$leads->next)): ?>
    <div class="chat-leads-loadmore">
        <button data-chat-loadmore="<?php echo self::$leads->next ?>" type="button" title="Carregar mais">
            Carregar mais
        </button>
    </div>
<?php endif ?>