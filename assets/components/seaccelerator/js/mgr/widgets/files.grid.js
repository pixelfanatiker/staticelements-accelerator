Seaccelerator.grid.Files = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'seaccelerator-grid-files'
		,url: Seaccelerator.config.connectorUrl
		,baseParams: { action: 'mgr/files/getList' }
		,save_action: 'mgr/files/updateFromGrid'
		,fields: ['actions','filename','category','type','path','content','source','mediasource']
		,paging: true
		,autosave: true
		,remoteSort: true
		,anchor: '97%'
		,autoExpandColumn: 'name'
		,columns: [{
			header: _('actions')
			,dataIndex: 'actions'
			,width: 30
			,sortable: true
			,renderer: { fn: this._renderActions ,scope:this }
		},{
			header: _('name')
			,dataIndex: 'filename'
			,width: 30
			,sortable: false
		},{
			header: _('category')
			,dataIndex: 'category'
			,width: 30
			,sortable: false
			,renderer: this.categoryRender
		},{
			header: _('type')
			,dataIndex: 'type'
			,width: 30
			,sortable: false
			,editable: false
			,renderer: this.typeRender
		},{
			header: _('source')
			,dataIndex: 'mediasource'
			,width: 30
			,sortable: false
			,editable: true
			//,renderer: { fn: this.getMediaSource ,scope:this }
			//,renderer: this.renderDynField.createDelegate(this,[this],true)
			,editor:{ xtype: 'combo', renderer: true}
		},{
			header: _('path')
			,dataIndex: 'path'
			,sortable: false
			,editable: false
		}]
		,tbar: [/*{
			text: _('seaccelerator.driver_create')
			,handler: { xtype: 'seaccelerator-window-driver-create' ,blankValues: true }
		},*/{
			xtype: 'textfield'
			,id: 'seaccelerator-search-filter'
			,width: 300
			,emptyText: _('seaccelerator.search')
			,listeners: {
				'change': {fn:this.search,scope:this}
				,'render': {fn: function(cmp) {
					new Ext.KeyMap(cmp.getEl(), {
						key: Ext.EventObject.ENTER
						,fn: function() {
							this.fireEvent('change',this);
							this.blur();
							return true;
						}
						,scope: cmp
					});
				},scope:this}
			}
		}]
	});
	Seaccelerator.grid.Files.superclass.constructor.call(this,config);
	this._makeTemplates();
};


Ext.extend(Seaccelerator.grid.Files,MODx.grid.Grid,{


	search: function(tf,nv,ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,getMenu: function() {
		return [{
			text: _('seaccelerator.context_menu.change_status')
			,handler: this.updateDriverStatus
		},'-',{
			text: _('seaccelerator.context_menu.update')
			,handler: this.updateDriverPage
		},{
			text: _('seaccelerator.context_menu.remove')
			,handler: this.removeDriver
		}];
	}

	,updateDriverStatus: function(btn,e) {
		if (!this.updateDriverWindow) {
			this.updateDriverWindow = MODx.load({
				xtype: 'seaccelerator-window-driver-status'
				,record: this.menu.record
				,listeners: {
					'success': {fn:this.refresh,scope:this}
				}
			});
		}
		this.updateDriverWindow.setValues(this.menu.record);
		this.updateDriverWindow.show(e.target);
	}

	,updateDriverPage: function() {
		MODx.loadPage(MODx.action['seaccelerator:update'], '?a=update&namespace=seaccelerator&id='+ this.menu.record.id);
	}

	,removeDriver: function() {
		MODx.msg.confirm({
			title: _('seaccelerator.remove')
			,text: _('seaccelerator.remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/nomination/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:this.refresh,scope:this}
			}
		});
	}

	,_renderActions: function(v,md,rec) {
		console.log(rec.data.nomination_status);
		if (rec.data.nomination_status == 0) {
			return this.tplStatusInactive.apply(rec.data);
		} else if (rec.data.nomination_status == 1) {
			return this.tplStatusActive.apply(rec.data);
		}
	}

	,_makeTemplates: function(rec) {
		this.tplStatusActive = new Ext.XTemplate('<i class="status icon icon-check-circle" title="Aktiv"></i>');
		this.tplStatusInactive = new Ext.XTemplate('<i class="status icon icon-exclamation-circle" title="Inaktiv"></i>');
	}

});
Ext.reg('seaccelerator-grid-files',Seaccelerator.grid.Files);




Seaccelerator.window.UpdateDriverStatus = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('seaccelerator.window.update_driver_status')
		,width: '400px'
		,url: Seaccelerator.config.connectorUrl
		,baseParams: {
			action: 'mgr/nomination/update'
		},

		xtype: "fieldset",
		autoHeight: true
		,fields: [{
			xtype: 'hidden'
			,name: 'id'
		},{
			xtype: 'textfield'
			,fieldLabel: _('seaccelerator.driver_name')
			,name: 'driver_name'
			,readOnly: true
			,anchor: '100%'
		},{
			xtype: 'modx-combo-boolean'
			,fieldLabel: _('seaccelerator.nomination_status')
			,name: 'nomination_status'
			,hiddenName: 'nomination_status'
			,value: config.record.status
			,anchor: '100%'
		}]
	});
	Seaccelerator.window.UpdateDriverStatus.superclass.constructor.call(this,config);
};
Ext.extend(Seaccelerator.window.UpdateDriverStatus,MODx.Window);
Ext.reg('seaccelerator-window-driver-status',Seaccelerator.window.UpdateDriverStatus);
