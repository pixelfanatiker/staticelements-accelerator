Ext.onReady(function() {
    MODx.load({ xtype: 'seaccelerator-page-home'});
});

Seaccelerator.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'seaccelerator-panel-home'
            ,renderTo: 'seaccelerator-panel-home-div'
        }]
    });
	Seaccelerator.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Seaccelerator.page.Home,MODx.Component);
Ext.reg('seaccelerator-page-home',Seaccelerator.page.Home);
