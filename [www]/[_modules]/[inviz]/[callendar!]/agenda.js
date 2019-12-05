//////////////////// Agenda file for CalendarXP 9.0 /////////////////
// This file is totally configurable. You may remove all the comments in this file to minimize the download size.
/////////////////////////////////////////////////////////////////////

//////////////////// Define agenda events ///////////////////////////
// Usage -- fAddEvent(year, month, day, message, action, bgcolor, fgcolor, bgimg, boxit, html, etc);
// Note:
// 1. the (year,month,day) identifies the date of the agenda event.
// 2. the message param will be shown as tooltip and in the status bar.
// 3. setting the action param to null will disable that date with a line-through effect.
// 4. bgcolor is the background color.
// 5. fgcolor is the font color. Setting it to ""(empty string) will hide the date.
// 6. bgimg is the url of the background image file in use with the specific date.
// 7. if boxit is set other than false or null value, the date will be drawn in a box using boxit value as the color, or bgcolor if boxit is true.
// 8. html is the HTML string to be injected into the agenda cell, e.g. an <img> tag.
// 9. etc is any object you would like to associate with the date, so that you can retrieve it later via the fGetEvent().
// ** REMEMBER to unlock corresponding bits of the gAgendaMask option in the theme.
/////////////////////////////////////////////////////////////////////

// fAddEvent(2003,12,2," Click me to active your email client. ","popup('mailto:any@email.address.org?subject=email subject')","#87ceeb","dodgerblue",null,true);
// fAddEvent(2004,4,1," Let's google. ","popup('http://www.google.com','_top')","#87ceeb","dodgerblue",null,"gold");
// fAddEvent(2004,9,23, "Hello World!\nYou can't select me.", null, "#87ceeb", "dodgerblue");




///////////// Recurring Events /////////////////////////
// fHoliday() provides you a flexible way to create recurring events easily.
// Once defined, it'll be used by the calendar engine to render each date cell.
// An agenda array [message, action, bgcolor, fgcolor, bgimg, boxit, html, etc] 
// is expected as return value, which are similar to the params of fAddEvent().
// Returning null value will result in default style as defined in the theme.
// ** REMEMBER to unlock corresponding bits of the gAgendaMask option in the theme.
////////////////////////////////////////////////////////
function fHoliday(y,m,d) {
	var rE=fGetEvent(y,m,d), r=null;

	// you may have sophisticated holiday calculation set here, following are only simple examples.
	

	
	return rE?rE:r;	// favor events over holidays
}


