Seaccelerator.grid.Elements = function(config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		tpl : new Ext.Template(
			'<p class="desc">{description}</p>'
		)
	});

	if (!config.tbar) {
		config.tbar = [
			/*{
				text: _('quick_create_'.type)
				,handler: {
				xtype: 'modx-window-quick-create-'+config.type
				,blankValues: true
			}
			},*/{
				text: _('seaccelerator.elements.actions.export_all')
				,handler: this.exportElementsAsStatic
				,cls: 'btn-export'
			}];
	}
	config.tbar.push('->',{
		xtype: 'modx-combo'
		,name: 'filter_category'
		,id: 'seaccelerator-filter-category'+config.type
		,emptyText: _('seaccelerator.elements.filter.by_category')
		,fields: ['id','category']
		,displayField: 'category'
		,valueField: 'id'
		,width: 250
		,pageSize: 10
		,url: Seaccelerator.config.connectorUrl
		,baseParams: {
			action: 'mgr/elements/getcategorylist'
			,type: config.type
		}
		,listeners: {
			'select': {fn: this.filterByCategory, scope: this}
		}
	},'-',{
		xtype: 'textfield'
		,name: 'filter_name'
		,id: 'seaccelerator-filter-name-'+config.type
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
	},{
		xtype: 'button'
		,id: 'seaccelerator-filter-clear-'+config.type
		,text: _('filter_clear')
		,handler: this.clearFilter
	});

	/*
	 var ec = new Ext.ux.grid.CheckColumn({
	 header: _('seaccelerator.elements.static')
	 ,dataIndex: 'static'
	 ,editable: false
	 ,width: 20
	 ,sortable: true
	 });
	 */

	this.cm = new Ext.grid.ColumnModel({
		columns: [this.exp,{
			header: _('id')
			,dataIndex: 'id'
			,width: 10
		},{
			header: _('status')
			,dataIndex: 'status'
			,width: 10
			,sortable: true
			,renderer: { fn: this._renderStatus ,scope:this }
		},{
			header: _('actions')
			,dataIndex: 'actions'
			,width: 40
			,sortable: true
			,renderer: { fn: this._renderActions ,scope:this }
		},{
			header: _('name')
			,dataIndex: (config.type=='template')?'templatename':'name'
			,width: 50
			,sortable: true
			,sortDir: 'ASC'
		},{
			header: _('source')
			,dataIndex: 'mediasource'
			,width: 30
			,sortable: false
			,editable: false
		},{
			header: _('seaccelerator.elements.file')
			,dataIndex: 'static_file'
			,sortable: false
			,editable: false
		},{
			header: _('seaccelerator.elements.static')
			,dataIndex: 'static'
			,width: 20
			,sortable: true
			,editable: true
			,renderer: this.renderDynField.createDelegate(this,[this],true)
		}]
		,tools: [{
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
		}]
		/* Editors are pushed here. I think that they should be in general grid
		 * definitions (modx.grid.js) and activated via a config property (loadEditor: true) */
		,getCellEditor: function(colIndex, rowIndex) {
			var field = this.getDataIndex(colIndex);
			if (field == 'static') {
				//var rec = config.store.getAt(rowIndex);
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
		,fields: ['id', 'status', 'actions', 'name', 'static','static_file','source','mediasource','description','category','snippet','plugincode','templatename','content','disabled']
		,id: 'seaccelerator-grid-elements-' + config.type + 's'
		,url: Seaccelerator.config.connectorUrl
		,baseParams: {
			action: 'mgr/elements/getlist'
			,type: config.type
		}
		,menuConfig: {
			defaultAlign: 'tl-b?'
			,enableScrolling: false
			,cls: 'sm-menu'
		}
		,clicksToEdit: 2
		,autosave: true
		,save_action: 'mgr/elements/updatefromgrid'
		,plugins: this.exp
		,autoHeight: true
		,paging: true
		,remoteSort: false
		,listeners: {
			'afterAutoSave': {fn:function() {
				this.refresh();
			},scope:this}
			,'afterEdit': {fn:function(e) {
				e.record.data.type = config.type;
			}}
		}
	});

	Seaccelerator.grid.Elements.superclass.constructor.call(this, config);
	this._makeTemplates();
};
Ext.extend(Seaccelerator.grid.Elements, MODx.grid.Grid, {

	renderDynField: function(v,md,rec,ri,ci,s,g) {
		var r = s.getAt(ri).data;
		var f,idx;
		var oz = v;
		var xtype = this.config.dynProperty;
		if (!r[xtype] || r[xtype] == 'combo-boolean') {
			f = MODx.grid.Grid.prototype.rendYesNo;
			oz = f(v == 1,md);
		} else if (r[xtype] === 'datefield') {
			f = Ext.util.Format.dateRenderer('Y-m-d');
			oz = f(v);
		} else if (r[xtype] === 'password') {
			f = this.rendPassword;
			oz = f(v,md);
		} else if (r[xtype].substr(0,5) == 'combo' || r[xtype] == 'list' || r[xtype].substr(0,9) == 'modx-combo') {
			var cm = g.getColumnModel();
			var ed = cm.getCellEditor(ci,ri);
			var cb;
			if (!ed) {
				r.xtype = r.xtype || 'combo-boolean';
				cb = this.createCombo(r);
				ed = new Ext.grid.GridEditor(cb);
				cm.setEditor(ci,ed);
			} else if (ed && ed.field && ed.field.xtype == 'modx-combo') {
				cb = ed.field;
			}
			if (r[xtype] != 'list') {
				f = Ext.util.Format.comboRenderer(ed.field);
				oz = f(v);
			} else if (cb) {
				idx = cb.getStore().find(cb.valueField,v);
				rec = cb.getStore().getAt(idx);
				if (rec) {
					oz = rec.get(cb.displayField);
				} else {
					oz = v;
				}
			}
		}
		return Ext.util.Format.htmlEncode(oz);
	}

	,onDirty: function(){
		if (this.config.panel) {
			Ext.getCmp(this.config.panel).fireEvent('fieldChange');
		}
	}

	,filterByCategory: function(category, selected){
		this.getStore().baseParams.categoryfilter = selected.id;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,filterByName: function(tf, newValue) {
		this.getStore().baseParams.namefilter = newValue || tf;
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}

	,clearFilter: function() {
		this.getStore().baseParams = {
			action: 'mgr/elements/getlist'
			,type: this.config.type
		};
		Ext.getCmp('seaccelerator-filter-category'+this.config.type).reset();
		Ext.getCmp('seaccelerator-filter-name-'+this.config.type).reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}, getMenu: function () {
		var m = [ {
			text: '<i class="icon icon-edit"></i>' + _('seaccelerator.elements.actions.quickupdate')
			,handler: this.editElement
		},{
			text: '<i class="icon icon-save"></i>' + _('seaccelerator.elements.actions.static')
			,handler: this.restoreToFile
			,scope: this
		}, {
			text: '<i class="icon icon-minus-square-o"></i>' + _('seaccelerator.elements.actions.delete')
			,handler: this.deleteFiles
			,scope: this
		}, {
			text: '<i class="icon icon-trash"></i>' + _('seaccelerator.elements.actions.delete_file_element')
			,handler: this.deleteFileAndElement
			,scope: this
		}];
		this.addContextMenuItem(m);
	}
	,_renderActions: function(v,md,rec) {
		return this.tplActions.apply(rec.data);
	}

	,_renderStatus: function(v,md,rec) {
		return this.tplStatus.apply(rec.data);
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
	,onClick: function(e) {
		var target = e.getTarget();
		var element = target.className.split(' ')[2];
		if(element === 'js_actionButton' || element === 'js_actionLink') {
			var action = target.className.split(' ')[3];
			var record = this.getSelectionModel().getSelected();
			this.menu.record = record;
			switch (action) {
				case 'js_editElement': this.editElement(record, e); break;
				case 'js_restoreToFile': this.restoreToFile(record, e); break;
				case 'js_syncToFile': this.syncToFile(record, e); break;
				case 'js_syncFromFile': this.syncFromFile(record, e); break;
				case 'js_exportToFile': this.syncToFile(record, e); break;
				case 'js_deleteElement': this.deleteElement(); break;
				case 'js_deleteFileElement': this.deleteFileAndElement(); break;
				case 'js_saveElement': this.syncToFile(); break;
				default:
					window.location = record.data.edit_action;
					break;
			}
		}
	}

	,editElement: function(rec, e) {
		var record;
		if (typeof rec.data === 'object') {
			record = rec.data;
		} else {
			record = this.menu.record;
		}
		record.clearCache = 1;
		var que = MODx.load({
			xtype: 'modx-window-quick-update-' + this.config.type
			,record: record
			,grid: this
			,listeners: {
				'success' : {fn:function(){
					this.refresh();
				},scope:this}
			}
		});
		que.reset();
		que.setValues(record);
		que.show(e.target);
	}

	,deleteElement: function() {
		MODx.msg.confirm({
			title: _('seaccelerator.elements.actions.element.delete.confirm.title')
			,text: _('seaccelerator.elements.actions.element.delete.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/elements/delete'
				,id: this.menu.record.id
				,type: this.menu.record.data.description.toLowerCase()
				,file: this.menu.record.data.static_file
				,del: "element"
			}
			,listeners: {
				'success': {fn:function(r) {
					this.refresh();
				} ,scope: this }
			}
		});
		return true;
	}

	,deleteFileAndElement: function() {
		MODx.msg.confirm({
			title: _('seaccelerator.elements.actions.delete_file_element.confirm.title')
			,text: _('seaccelerator.elements.actions.delete_file_element.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/elements/delete'
				,id: this.menu.record.id
				,type: this.menu.record.data.description.toLowerCase()
				,file: this.menu.record.data.static_file
				,del: "both"
			}
			,listeners: {
				'success': {fn:function(r) {
					this.refresh();
				} ,scope: this }
			}
		});
		return true;
	}

	,restoreToFile: function () {
		MODx.msg.confirm({
			title: _('seaccelerator.elements.actions.restore.tofile.confirm.title')
			,text: _('seaccelerator.elements.actions.restore.tofile.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/elements/sync.class'
				,id: this.menu.record.id
				,type: this.menu.record.data.description.toLowerCase()
				,file: this.menu.record.data.static_file
				,sync: "tofile"
			}
			,listeners: {
				'success': {fn:function(r) {
					this.refresh();
				} ,scope: this }
			}
		});
		return true;
	}

	,syncToFile: function () {
		MODx.msg.confirm({
			title: _('seaccelerator.elements.actions.sync.tofile.confirm.title')
			,text: _('seaccelerator.elements.actions.sync.tofile.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/elements/sync.class'
				,id: this.menu.record.id
				,type: this.menu.record.data.description.toLowerCase()
				,file: this.menu.record.data.static_file
				,sync: "tofile"
			}
			,listeners: {
				'success': {fn:function(r) {
					this.refresh();
				} ,scope: this }
			}
		});
		return true;
	}

	,syncFromFile: function () {
		MODx.msg.confirm({
			title: _('seaccelerator.elements.actions.sync.fromfile.confirm.title')
			,text: _('seaccelerator.elements.actions.sync.fromfile.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/elements/sync.class'
				,id: this.menu.record.id
				,type: this.menu.record.data.description.toLowerCase()
				,file: this.menu.record.data.static_file
				,sync: "fromfile"
			}
			,listeners: {
				'success': {fn:function(r) {
					this.refresh();
				} ,scope: this }
			}
		});
		return true;
	}

	,exportElementsAsStatic: function () {
		MODx.msg.confirm({
			title: _('seaccelerator.elements.actions.tostatic.all.confirm.title')
			,text: _('seaccelerator.elements.actions.tostatic.all.confirm.text')
			,url: this.config.url
			,params: {
				action: 'mgr/elements/saveall'
			}
			,listeners: {
				'success': {fn:function(r) {
					this.refresh();
				} ,scope: this }
			}
		});
		return true;
	}

});

Ext.reg('seaccelerator-grid-elements-chunks', Seaccelerator.grid.Elements);
Ext.reg('seaccelerator-grid-elements-plugins', Seaccelerator.grid.Elements);
Ext.reg('seaccelerator-grid-elements-snippets', Seaccelerator.grid.Elements);
Ext.reg('seaccelerator-grid-elements-templates', Seaccelerator.grid.Elements);
