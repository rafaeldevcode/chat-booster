<section class="chat-container" data-chat-content="chat">
    <div class="chat-header">
        <div class="chat-user">
            <div class="chat-user-photo">
                <img src="<?php echo empty(self::$settings) ? '...' : self::$settings->avatar ?>" alt="<?php echo empty(self::$settings) ? 'Não definido' : self::$settings->name ?>">
            </div>

            <p class="chat-user-name"><?php echo empty(self::$settings) ? 'Não definido' : self::$settings->name ?></p>
        </div>

        <div class="chat-actions">
            <button type="button" title="To close" data-chat="close">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-messages">  
            <div data-chat-messages="chat" data-chat-from="0" data-chat-to="1">
                <div class="message message-from"></div>
            </div>
        </div>
    </div>

    <div class="chat-footer">
        <form action="?" method="POST" class="chat-form-send" data-chat-form="chat">
            <?php require __DIR__.'/partials/form-send-message.php'; ?>
        </form>
    </div>
</section>
