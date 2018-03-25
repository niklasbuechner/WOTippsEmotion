Ext.define('Shopware.apps.WOTippsEmotionCache.controller.Direct', {

    extend: 'Enlight.app.Controller',

    clearEmotion: function() {
        var url = "{url controller=WOTippsEmotionCache action=clear}";
        
        Ext.Ajax.request({
            url: url,
            method: 'POST',
            success: function(operation, opts) {
                var response = Ext.decode(operation.responseText);

                if (response.success === false) {
                    
                    Shopware.Notification.createGrowlMessage({
                        title: 'Einkaufswelten',
                        text: 'Der Einkaufsweltencache konnte nicht geleert.',
                        width: 350
                    });
                }
                else
                {
                    Shopware.Notification.createStickyGrowlMessage({
                        title: 'Einkaufswelten',
                        text: 'Der Einkaufsweltencache wurde geleert.',
                        width: 350
                    });
                }
                
                me.subApplication.handleSubAppDestroy(null);
            }
        });
        
        return;
    }
});

