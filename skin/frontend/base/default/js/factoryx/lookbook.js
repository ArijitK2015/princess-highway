/**
 *	Function to save the last positions of the lookbook
 */
function saveLastPositions()
{
	// Create a 60 days expiry date
	var expiryDate = new Date();
	expiryDate.setDate(expDate.getDate() + 60);
	// Get the last positions
	// Container
	var lastContainerPos = document.getElementById('container').scrollLeft;
	// Scale
	var lastScalePos = document.getElementById('scale').style.left;
	// Save them in cookies
	Mage.Cookies.set('lastContainerPos', lastContainerPos, expiryDate);
	Mage.Cookies.set('lastScalePos', lastScalePos, expiryDate);
}

jQuery(document).ready(function()
{
	// Retrieve them
	var lastContainerPos = Mage.Cookies.get("lastContainerPos");
	var lastScalePos = Mage.Cookies.get("lastScalePos");
	
	// Reset the container position
	if (lastContainerPos)
	{
		document.getElementById('container').scrollLeft = lastContainerPos;
		// Erase the cookie
		var eraseDate = new Date();
		eraseDate.setTime(eraseDate.getTime()+(-1*24*60*60*1000));
		Mage.Cookies.set('lastContainerPos', "", eraseDate);
	}
	
	// Reset the scale position
	if (lastScalePos)
	{
		document.getElementById('scale').style.left = lastScalePos;
		// Erase the cookie
		var eraseDate = new Date();
		eraseDate.setTime(eraseDate.getTime()+(-1*24*60*60*1000));
		Mage.Cookies.set('lastScalePos', "", eraseDate);
	}
});