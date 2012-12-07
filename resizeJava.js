function openAndResize(link)
{
this.resizeSelf();
window.open(link,"help","scrollbars,resizable")
}

function resizeSelf()
{
var c=450;
var a=240;
var b=570;
if(screen)
{
c=(screen.availWidth-a);
b=(screen.availHeight)
}
window.resizeTo(c,b);
window.moveTo(0,0);
return false
}
