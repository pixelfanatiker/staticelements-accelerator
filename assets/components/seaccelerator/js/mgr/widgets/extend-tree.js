/**
 * Generates the Element Tree
 *
 * @class MODx.tree.Element
 * @extends MODx.tree.Tree
 * @param {Object} config An object of options.
 * @xtype modx-tree-element
 */
MODx.tree.SeacceleratorTreeExtender = function(config) {
	console.log("SeacceleratorTreeExtender");
	config = config || {};
	Ext.applyIf(config,{
		useArrows: true
		,autoScroll: true
		,animate: true
		,enableDD: true
		,enableDrop: true
		,ddAppendOnly: false
		,containerScroll: true
		,collapsible: true
		,border: false
		,autoHeight: true
		,rootVisible: true
		,loader: tl
		,header: false
		,hideBorders: true
		,bodyBorder: false
		,cls: 'modx-tree'
		,root: root
		,preventRender: false
		,stateful: true
		,menuConfig: {
			defaultAlign: 'tl-b?',
			enableScrolling: false,
			listeners: {
				show: function() {
					var node = this.activeNode;
					if (node)
						node.ui.addClass('x-tree-selected');
				},
				hide: function() {
					var node = this.activeNode;
					if (node){
						node.isSelected() || node.ui.removeClass('x-tree-selected');
					}
				}
			}
		}
	});
	MODx.tree.SeacceleratorTreeExtender.superclass.constructor.call(this,config);
	this.setup(config);
};

Ext.extend(MODx.tree.SeacceleratorTreeExtender,MODx.tree.Tree,{

	/**
	 * Sets up the tree and initializes it with the specified options.
	 */
	setup: function(config) {
		config.listeners = config.listeners || {};
		config.listeners.render = {
			fn: function() {
				if (config.autoExpandRoot !== false || !config.hasOwnProperty('autoExpandRoot')) {
					this.root.expand();
				}
				var tl = this.getLoader();
				Ext.apply(tl,{fullMask : new Ext.LoadMask(this.getEl())});
				tl.fullMask.removeMask=false;
				tl.on({
					'load' : function(){this.fullMask.hide();}
					,'loadexception' : function(){this.fullMask.hide();}
					,'beforeload' : function(){this.fullMask.show();}
					,scope : tl
				});
			}
			,scope: this
		};
		MODx.tree.SeacceleratorTreeExtender.superclass.constructor.call(this,config);
		this.addEvents('afterSort','beforeSort');
		this.cm = new Ext.menu.Menu(config.menuConfig);
		this.on('contextmenu',this._showContextMenu,this);
		console.log("SeacceleratorTreeExtender");
	}

	/**
	 * Shows the current context menu.
	 * @param {Ext.tree.TreeNode} n The current node
	 * @param {Ext.EventObject} e The event object run.
	 */
	,_showContextMenu: function(n,e) {
		this.cm.activeNode = n;
		this.cm.removeAll();
		if (n.attributes.menu && n.attributes.menu.items) {
			this.addContextMenuItem(n.attributes.menu.items);
			this.cm.show(n.getUI().getEl(),'t?');
		} else {
			var m = [];
			switch (n.attributes.classKey) {
				case 'root':
					//m = this._getRootMenu(n);
					break;
				case 'modCategory':
					//m = this._getCategoryMenu(n);
					break;
				default:
					m = this._getElementMenu(n);
					break;
			}
			this.addContextMenuItem(m);
		}
		e.stopEvent();
	}


	,_getElementMenu: function(n) {
		var a = n.attributes;
		var ui = n.getUI();
		var m = [];

		m.push('-');
		m.push({
			text: 'Set as static element'
			,handler: this.removeElement
		});

		return m;
	}

});

Ext.reg('modx-tree-element',MODx.tree.SeacceleratorTreeExtender);
