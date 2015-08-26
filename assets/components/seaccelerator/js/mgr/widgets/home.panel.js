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
			,stateful: true
			,stateId: 'seaccelerator-tabpanel-home'
			,stateEvents: ['tabchange']
			//,activeItem: 0
			,getState: function() {
				return { activeTab:this.items.indexOf(this.getActiveTab()) };
			}

			,items: [{
				title:  _('seaccelerator.tab_files')
				,id: 'seaccelerator-tab-files'
				,layout: 'form'
				,items: [{
					border: false
					,bodyCssClass: 'panel-desc'
					,items: [{
						html: '<p>' + _('seaccelerator.tab_files.description') + '</p>'
						,border: false
						,style: {
							lineHeight: '30px'
						}
					}]
				},{
					bodyCssClass: 'main-wrapper'
					,border: false
					,items: [{
						xtype: 'seaccelerator-grid-files'
					}]
				}]
			},{
				title: _('chunks')
				,id: 'seaccelerator-tab-chunks'
				,layout: 'form'
				,items: [{
					html: '<p>'+_('chunks')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
				},{
					xtype: 'seaccelerator-grid-elements-chunks'
					,preventSaveRefresh: true
					,cls: 'main-wrapper'
					,type: 'chunk'
				}]
			},{
				title: _('plugins')
				,id: 'seaccelerator-tab-plugins'
				,layout: 'form'
				,items: [{
					html: '<p>'+_('plugins')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
				},{
					xtype: 'seaccelerator-grid-elements-plugins'
					,preventSaveRefresh: true
					,cls: 'main-wrapper'
					,type: 'plugin'
				}]
			},{
				title: _('snippets')
				,id: 'seaccelerator-tab-snippets'
				,layout: 'form'
				,items: [{
					html: '<p>' + _('snippets') + '</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
				},{
					xtype: 'seaccelerator-grid-elements-snippets'
					,preventSaveRefresh: true
					,cls: 'main-wrapper'
					,type: 'snippet'
				}]
			},{
				title: _('templates')
				,id: 'seaccelerator-tab-templates'
				,layout: 'form'
				,items: [{
					html: '<p>'+_('templates')+'</p>'
					,border: false
					,bodyCssClass: 'panel-desc'
				},{
					xtype: 'seaccelerator-grid-elements-templates'
					,preventSaveRefresh: true
					,cls: 'main-wrapper'
					,type: 'template'
				}]
			},{
				title: _('seaccelerator.tab_settings')
				,id: 'seaccelerator-tab-settings'
				,items: [{
					//xtype: 'seaccelerator-tab-common'
				}]
			}]
		}]
	});
	Seaccelerator.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Seaccelerator.panel.Home,MODx.Panel);
Ext.reg('seaccelerator-panel-home',Seaccelerator.panel.Home);
