jQuery(document).ready(function()
{
	jQuery('.pop').cluetip({sticky: true, titleAttribute: 'title', local:true, cursor: 'pointer', dropShadow: true, activation: 'click' });
	jQuery('.jt').cluetip({cluetipClass: 'jtip', positionBy: false, arrows: false, dropShadow: true, local:true, mouseOutClose: true});
	jQuery('.jt_sticky').cluetip({cluetipClass: 'jtip', positionBy: false, arrows: false, dropShadow: true, local:true, closePosition: 'title', sticky:true});
	//jQuery('#cluetip').jqDrag();
});