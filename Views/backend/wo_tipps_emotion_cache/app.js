Ext.define('Shopware.apps.WOTippsEmotionCache', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.WOTippsEmotionCache',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: ['Direct'],

    views: [],
    
    windowClasses: [],

    models: [],
    stores: [],

    launch: function()
    {
        this.getController('Direct').clearEmotion();
    }
});