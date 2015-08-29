var Seaccelerator = function(config) {
	config = config || {};
	Seaccelerator.superclass.constructor.call(this,config);
};
Ext.extend(Seaccelerator,Ext.Component,{
	page:{},
	window:{},
	grid:{},
	tree:{},
	panel:{},
	combo:{},
	config: {}
});
Ext.reg('seaccelerator',Seaccelerator);

Seaccelerator = new Seaccelerator();
