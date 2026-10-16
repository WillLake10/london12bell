goog.require('goog.dom.DomHelper');
goog.require('goog.ui.RoundedPanel');

dom_ = new goog.dom.DomHelper();
cornerdivs = dom_.$$('div','corners');

for(var i = 0; i < cornerdivs.length; i++)
{
	corner = cornerdivs[i];
	if(corner.currentStyle)
		var col = corner.currentStyle['backgroundColor'];
	else if (window.getComputedStyle)
		var col = document.defaultView.getComputedStyle(corner,null).getPropertyValue('background-color');
	dom_.setProperties(corner, {'style':'background-color: white'}); 
	var rp = goog.ui.RoundedPanel.create(10, 0, '#000000', col, 15);
	rp.decorate(corner);
}