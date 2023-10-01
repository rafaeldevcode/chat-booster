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