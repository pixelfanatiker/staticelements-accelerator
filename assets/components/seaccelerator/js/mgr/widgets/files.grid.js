Seaccelerator.grid.Files = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		tpl : new Ext.Template(
			'<p class="desc">{description}</p>'
		)
	});

	if (!config.tbar) {
		config.tbar = [{
			xtype: 'button'
			,text: '<i class="icon icon-cubes"></i>' + _('seaccelerator.files.actions.generate_all')
			,cls:'primary-button x-btn-noicon'
			,style: {
				float: 'left'
				,marginRight: '20px'
			}
			,listeners: {
				click: function(){}
			}
			,handler:this.createMultipleElements
		}/*,{
			xtype: 'button'
			,text: '<i class="icon icon-refresh"></i>' + _('seaccelerator.files.actions.sync_all')
			,cls:'x-btn-text'
			,style: {
				float: 'left'
				,marginRight: '20px'
			}
			,listeners: {
				click: function(){

					Ext.Msg.show({
						title: _('please_wait')
						,msg: _('seaccelerator.files.actions.syncronizing')
						,width: 240
						,progress:true
						,closable:false
					});

					MODx.util.Progress.reset();
					for(var i = 1; i < 20; i++) {
						setTimeout('MODx.util.Progress.time('+i+','+MODx.util.Progress.id+')',i*1000);
					}
					MODx.Ajax.request({
						url: Seaccelerator.config.connectorUrl
						,params: {

							action: 'common/syncall'
							,root: '111111'
						}
						,listeners: {
							'success': {fn:function(r) {
								MODx.util.Progress.reset();
								Ext.Msg.hide();
							},scope:this}
							,'failure': {fn:function(r) {
								MODx.util.Progress.reset();
								Ext.Msg.hide();
								MODx.form.Handler.errorJSON(r);
								return false;
							},scope:this}
						}
					});
				}
			}
		}*/];
	}
	config.tbar.push('->',{
		xtype: 'modx-combo'
		,name: 'filter_type'
		,id: 'seaccelerator-filter-type-files'
		,emptyText: _('seaccelerator.elements.filter_by_type')
		,fields: ['id','type']
		,displayField: 'type'
		,valueField: 'id'
		,width: 250
		,pageSize: 10
		,url: Seaccelerator.config.connectorUrl
		,baseParams: {
			action: 'mgr/files/getTypeList'
			,type: config.type
		}
		,listeners: {
			'select': {fn: this.filterByType, scope: this}
		}
	},'-',{
		xtype: 'textfield'
		,name: 'filter_name'
		,id: 'seaccelerator-filter-name-files'
		,emptyText: _('seaccelerator.elements.filter_by_name')+'...'
		,listeners: {
			'change': {fn: this.filterByName, scope: this}
			,'render': {fn: function(cmp) {
				new Ext.KeyMap(cmp.getEl(), {
					key: Ext.EventObject.ENTER
					,fn: this.blur
					,scope: cmp
				});
			},scope:this}
		}
	}/*,{
		xtype: 'button'
		,id: 'seaccelerator-filter-clear-files'
		,text: _('filter_clear')
		,handler: this.clearFilter
	}*/);

	this.cm = new Ext.grid.ColumnModel({
		columns: [{
			header: _('actions')
			,dataIndex: 'actions'
			,width: 30
			,sortable: true
			,renderer: { fn: this._renderActions ,scope:this }
		},{
			header: _('name')
			,dataIndex: 'filename'
			,width: 50
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
			,width: 20
			,sortable: false
			,editable: true
			//,renderer: { fn: this.getMediaSource ,scope:this }
			//,renderer: this.renderDynField.createDelegate(this,[this],true)
			,editor:{ xtype: 'combo', renderer: true}
		},{
			header: _('path')
			,dataIndex: 'path'
			,width: 30
			,sortable: false
			,editable: false
		}]
		/*,tools: [{
			id: 'plus'
			,qtip: _('expand_all')
			,handler: this.expandAll
			,scope: this
		},{
			id: 'minus'
			,hidden: true
			,qtip: _('collapse_all')
			,handler: this.collapseAll
			,scope: this
		}]*/


		/* Editors are pushed here. I think that they should be in general grid
		 * definitions (modx.grid.js) and activated via a config property (loadEditor: true) */
		,getCellEditor: function(colIndex, rowIndex) {
			var field = this.getDataIndex(colIndex);
			if (field == 'static') {
				var rec = config.store.getAt(rowIndex);
				var o = MODx.load({
					xtype: 'combo-boolean'
				});
				return new Ext.grid.GridEditor(o);
			}
			return Ext.grid.ColumnModel.prototype.getCellEditor.call(this, colIndex, rowIndex);
		}

	});

	Ext.applyIf(config,{
		cm: this.cm
		,fields: ['actions','filename','category','type','path','content','source','mediasource']
		,id: 'seaccelerator-grid-elements-files'
		,url: Seaccelerator.config.connectorUrl
		,menuConfig: {
			defaultAlign: 'tl-b?'
			,enableScrolling: false
			,cls: 'sm-menu'
		}
		,baseParams: {
			action: 'mgr/files/getList'
		}
		,clicksToEdit: 2
		,autosave: true
		,save_action: 'mgr/files/updateFromGrid'
		,plugins: this.exp
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,listeners: {
			'afterAutoSave': {fn:function() {
				this.refresh();
			},scope:this}
			,'afterEdit': {fn:function(e) {
				e.record.data.type = config.type;
			}}
		}
	});

	Seaccelerator.grid.Files.superclass.constructor.call(this, config);
	this._makeTemplates();
};


Ext.extend(Seaccelerator.grid.Files,MODx.grid.Grid,{


	search: function(tf,nv,ov) {
		var s = this.getStore();
		s.baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,typeRender: function(r) {
		if(r == 0){
			return 'no_type';
		}
		return r;
	}

	,categoryRender: function(r) {
		//console.log(r);
		if(r == 0){
			return _('no_category');
		}
		return r;
	}

	,onDirty: function(){
		if (this.config.panel) {
			Ext.getCmp(this.config.panel).fireEvent('fieldChange');
		}
	}

	,filterByType: function(type, selected){
		this.getStore().baseParams = {
			action: 'files/getlist'
			,type: selected.id
		};
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,filterByName: function(tf, newValue) {
		this.getStore().baseParams.namefilter = newValue || tf;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,updateGrid: function() {
		alert(123);
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function() {
		this.getStore().baseParams = {
			action: 'files/getlist'
		};

		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,getMenu: function(r) {
		var m = [
			{
				text: '<i class="icon icon-check-square-o"></i>' + _('seaccelerator.files.actions.generate')
				,handler: this.createSingleElement
			},{
				text: '<i class="icon icon-edit"></i>' + _('seaccelerator.files.actions.edit_file')
				,handler: this.editFile
			},{
				text: '<i class="icon icon-trash"></i>' +_('seaccelerator.files.actions.delete_file')
				,handler: this.deleteFile
			}
		];

		this.addContextMenuItem(m);
	}

	,onClick: function(e){
		var target = e.getTarget();
		var element = target.className.split(' ')[2];
		if(element === 'js_actionButton' || element === 'js_actionLink') {
			var action = target.className.split(' ')[3];
			var record = this.getSelectionModel().getSelected();
			this.menu.record = record;

			switch (action) {
				case 'js_createElement': this.createSingleElement(record); break;
				case 'js_deleteFile': this.deleteFile(record); break;
				case 'js_editFile': this.editFile(record); break;
				default:
					//window.location = record.data.edit_action;
					break;
			}
		}
	}

	,createSingleElement: function(record){
		var filename, path, category;
		if (typeof record.data !== "undefined") {
			filename = record.data.filename;
			path = record.data.path;
			category = record.data.category;
		} else {
			filename = this.menu.record.filename;
			path = this.menu.record.path;
			category = this.menu.record.category;
		}

		MODx.msg.confirm({
			title: _('seaccelerator.files.actions.create_element')
			,text: _('seaccelerator.files.actions.create_element_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/files/createElements'
				,filename: filename
				,path: path
				,category: category
			}
			,listeners: {
				'success': {fn:function(){
					this.refresh();
				},scope:this}
			}
		});
	}

	,createMultipleElements: function(btn,e) {
		Ext.Msg.show({
			title: _('please_wait')
			,msg: _('seaccelerator.files.actions.create.processing')
			,width: 240
			,progress:true
			,closable:false
		});

		MODx.util.Progress.reset();
		for(var i = 1; i < 20; i++) {
			setTimeout('MODx.util.Progress.time('+i+','+MODx.util.Progress.id+')',i*1000);
		}

		MODx.Ajax.request({
			url: Seaccelerator.config.connectorUrl
			,params: {
				action: 'mgr/files/createElements'
				,process: 'multi'
			}
			,listeners: {
				'success': {fn:function(r) {
					MODx.util.Progress.reset();
					Ext.Msg.hide();
					this.refresh();
				},scope:this}
				,'failure': {fn:function(r) {
					MODx.util.Progress.reset();
					Ext.Msg.hide();
					return false;
				},scope:this}
			}
		});
	}

	,editFile: function(record){
		var rec;
		if (typeof record.data !== "undefined") {
			rec = record.data;
		} else {
			rec = this.menu.record;
		}

		rec.name = rec.filename;
		rec.source = '0';
		rec.file = rec.path;
		rec.clearCache = 1;

		var que = MODx.load({
			xtype: 'modx-window-file-quick-update'
			,url: this.config.url
			,record: rec
			,grid: this
			,action: 'mgr/files/update'
			,listeners: {
				'success': {fn:function(){
					this.refresh();
				},scope:this}
			}
		});

		que.reset();
		que.setValues(rec);
		que.show(e.target);
	}

	,deleteFile: function(record) {
		var path, filename, mediasource;
		if (typeof record.data !== "undefined") {
			path = record.data.path;
			filename = record.data.path;
			mediasource = record.data.path;
		} else {
			path = this.menu.record.path;
			filename = this.menu.record.filename;
			mediasource = this.menu.record.mediasource;
		}

		MODx.msg.confirm({
			title: _('seaccelerator.files.actions.delete.confirm.title')
			,text: _('seaccelerator.files.actions.delete.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/files/delete'
				,path: path
				,filename: filename
				,mediasource: mediasource
			}
			,listeners: {
				'success': {fn:function(){
					this.refresh();
				},scope:this}
			}
		});
	}

	,_renderActions: function(v,md,rec) {
		return this.tplActions.apply(rec.data);
	}

	,_makeTemplates: function() {
		this.tplActions = new Ext.XTemplate('<tpl for=".">' +
			'<div class="holder actions">' +
			'<tpl for="actions">' +
			'<i class="icon icon-{className}" title="{text}"></i>' +
			'</tpl>' +
			'</div>' +
			'</tpl>');
		this.tplStatus = new Ext.XTemplate('<tpl for=".">' +
			'<div class="holder status">' +
			'<tpl for="status">' +
			'<i class="icon icon-{className}" title="{text}"></i>' +
			'</tpl>' +
			'</div>' +
			'</tpl>');
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
