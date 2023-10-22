<section class="chat-container" data-chat-content="settings">
    <div class="chat-header">
        <div class="chat-user">
            <div class="chat-user-photo">
                <img data-chat-avatar="https://img.freepik.com/icones-gratis/do-utilizador_318-563642.jpg?w=360" src="https://img.freepik.com/icones-gratis/do-utilizador_318-563642.jpg?w=360" alt="Rafael Vieira">
            </div>

            <p class="chat-user-name" data-chat-name="Configurações">Configurações</p>
        </div>

        <div class="chat-actions">
            <button type="button" title="Leads" data-chat-menu="leads" data-chat-button="active" <?php echo (!empty(self::$has_migrations) || !self::$has_file_setting) ? 'disabled' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                </svg>
            </button>

            <button type="button" title="Configurações" data-chat-menu="settings" data-chat-button="inactive" <?php echo (!empty(self::$has_migrations) || !self::$has_file_setting) ? 'disabled' : '' ?>>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                </svg>
            </button>

            <button type="button" title="To close" data-chat="close">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-messages">
            <?php if(!empty(self::$has_migrations)):
                require __DIR__.'/partials/run-migrations.php'; 
            elseif(!self::$has_file_setting):
                require __DIR__.'/partials/configure-database.php';    
            endif ?>

            <div class="chat-messages-content">
                <?php if(empty(self::$has_migrations) && self::$has_file_setting): 
                    require __DIR__.'/partials/form-settings.php';
                    require __DIR__.'/partials/list-leads.php';
                endif;?>
            </div>

            <div class="chat-messages-list" data-chat-messages="settings"></div>
        </div>
    </div>

    <div class="chat-footer">
        <form action="?" method="POST" class="chat-form-send" data-chat-form="settings">
            <?php require __DIR__.'/partials/form-send-message.php'; ?>
        </form>
    </div>
</section>
