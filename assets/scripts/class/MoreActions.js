$('[data-more-actions="button"]').on('click', () => {
    const moreActions = $('#more-actions');

    if(moreActions.attr('data-more-actions') && moreActions.attr('data-more-actions') == 'true'){
        moreActions.attr('data-more-actions', false);

        setTimeout(() => {
            moreActions.removeAttr('data-more-actions');
        }, 500);
    }else{
        moreActions.attr('data-more-actions', true);
    }
});

class Chat{
    static init(){
        $('[data-chat-open]').on('click', (event) => {
            const button = $(event.target).attr('data-chat-open') ? $(event.target) : $(event.target).parent();
            const dataChat = button.attr('data-chat-open') ? button.attr('data-chat-open') : button.parent().attr('data-chat-open');
        
            this.open(dataChat);
        });
    }

    static open(selector) {
        const element = $(`[data-chat-content="${selector}"]`);

        element.attr('data-message-box', true);

        this.close(selector);
    }

    static close(selector){
        $('[data-chat="close"]').on('click', () => {
            const element = $(`[data-chat-content="${selector}"]`);

            element.attr('data-message-box', false);

            setTimeout(() => {
                moreActions.removeAttr('data-message-box');
            }, 600);
        });
    }
}

Chat.init();
