(function( $ ){

    function calculateResults(startX, startY, endX, endY, tresholdX, tresholdY){
        var swipeDirection = {up:false, right:false, down: false, left:false};
		// console.log(startX + ':' + endX + ' = ' + (startX - endX) + '>' + tresholdX);
        if(startX > endX && startX - endX >= tresholdX)
            swipeDirection.left = true;
        else if(startX < endX && endX - startX >= tresholdX && startX <=30)
            swipeDirection.right = true;
        
        if(startY < endY && endY - startY >= tresholdY)
            swipeDirection.down = true
        else if(startY > endY && startY - endY >=tresholdY)
            swipeDirection.up = true;

        return swipeDirection;

    }
    $.fn.onSwipe = function(f, timeTreshold, tresholdX, tresholdY){
        if(jQuery.isFunction(f)){ //We are only going to do our thing if the user passed a function

        if(typeof timeTreshold === 'undefined' || timeTreshold === null)
            timeTreshold = 50;//ms

        if(typeof tresholdX === 'undefined' || tresholdX === null)
            tresholdX = 30;//px
        
        if(typeof tresholdY === 'undefined' || tresholdY === null)
            tresholdY = 30;//px

        var startX,  startY; //Position when touch begins
        var endX, endY; //Position when touch ends

        var time; //Our timer variable
        var totalTime = 0; //Total time that the swipe took

        //When a touch starts on this element.
            //We can start a timer, and start getting coordinates.
        $(this).on("touchstart", function(e){

            //Let's get our touch coordinates
            startX = e.touches[0].clientX; //This is where touchstart coordinates are stored
            startY = e.touches[0].clientY;

            time = setInterval(function(){ //Let's see how long the swipe lasts.
                totalTime += 10;
            }, 10);
        });

        $(this).on("touchend", function(e){

            endX = e.changedTouches[0].clientX; //This is where touchend coordinates are stored.
            endY = e.changedTouches[0].clientY;

            clearInterval(time); //Let's stop calculating time and free up resources.

            if(totalTime >= timeTreshold) //If swipe time is less than our treshold we won't do anything. Useful for preventing spam and accidental swipes.
                f(calculateResults(startX, startY, endX, endY, tresholdX, tresholdY)); //Send results to user's function

            

            totalTime = 0;
        });
        } else console.error("You need to pass a function in order to process swipe data.");

        return $(this);
    }
})( jQuery );