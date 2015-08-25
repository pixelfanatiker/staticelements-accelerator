Seaccelerator.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config,{
		border: false
		,baseCls: 'modx-formpanel'
		,cls: 'container'
		,items: [{
			html: '<h2>'+_('seaccelerator.title')+'</h2>'
			,border: false
			,cls: 'modx-page-header'
		},{
			xtype: 'modx-tabs'
			,defaults: { border: false ,autoHeight: true }
			,border: true
			,items: [{
				title: _('seaccelerator.tab_files')
				,defaults: { autoHeight: true }
				,items: [{
					html: '<p>'+_('seaccelerator.files.description')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
				},{
					xtype: 'seaccelerator-grid-files'
					,cls: 'main-wrapper'
					,preventRender: true
				}]
			}]
		}]
	});
	Seaccelerator.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Seaccelerator.panel.Home,MODx.Panel);
Ext.reg('seaccelerator-panel-home',Seaccelerator.panel.Home);
