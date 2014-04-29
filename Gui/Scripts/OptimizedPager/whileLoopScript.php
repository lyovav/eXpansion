

if(pager_moveScroll){

    if(pager_firstClick){
	pager_scrollYOriginPosition = ScrollBar.RelativePosition.Y;
    }
    pager_firstClick = False;

    declare ypos = pager_scrollYOriginPosition + pager_deltaMouseYPosition;
    
    if(ypos >= pagerStartPos){
	ScrollBar.RelativePosition.Y = pagerStartPos;
    }else if(ypos < pagerStopPosition){
	ScrollBar.RelativePosition.Y = pagerStopPosition;
    }else{
	ScrollBar.RelativePosition.Y = pager_scrollYOriginPosition + pager_deltaMouseYPosition;
    }

    declare maxDelta = pagerStartPos - pagerStopPosition;
    
    currentIndex = MathLib::NearestInteger((ScrollBar.RelativePosition.Y/maxDelta)*maxIndex) * -1;
}

if(oldIndex != currentIndex){
    //Redraw
    oldIndex = currentIndex;
    for(i, 0, rowsPerPage) {                   
	for(r, 0, itemsPerRow-1) { 
	    declare CMlLabel item = labels[i][r];

	    if (item != Null) {     
		if (textData.count >  i+currentIndex && i+currentIndex >= 0) {
		    item.SetText(textData[i+currentIndex][r]);                                                          
		}
	    }
	}                                                                                 
    }
    
    if (textData.count > rowsPerPage) {
	    ScrollUp.Opacity = 1.0;
	    ScrollDown.Opacity = 1.0;
	
	    if (currentIndex == 0) {
	        ScrollUp.Opacity = disabledOpacity;	
	    } 
	
	    if (currentIndex == maxIndex) {
		ScrollDown.Opacity = disabledOpacity;	
	    } 
	}
	
}

foreach (Event in PendingEvents) {

    if(Event.Type == CMlEvent::Type::MouseClick){
	if (Event.ControlId == "ScrollBar")  {
	    pager_startMouseYPosition = MouseY;
	    pager_firstClick = True;
	    pager_moveScroll = True;
	}else if (Event.ControlId == "ScrollDown")  {
	    if (textData.count > rowsPerPage) {
		currentIndex = currentIndex + 5;
		if(currentIndex > maxIndex){
		    currentIndex = maxIndex;
		}
		declare maxDelta = pagerStartPos - pagerStopPosition;
		ScrollBar.RelativePosition.Y = -1*(maxDelta/maxIndex)*currentIndex;
	    }
	}else if (Event.ControlId == "ScrollUp")  {
		if (textData.count > rowsPerPage) {
		    currentIndex = currentIndex - 5;
		    if(currentIndex < 0){
			    currentIndex = 0;
		    }
		    declare maxDelta = pagerStartPos - pagerStopPosition;
		    ScrollBar.RelativePosition.Y = -1*(maxDelta/maxIndex)*currentIndex;
		}
	}
    }
    
    if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {    
	if (Event.Control.HasClass("eXpOptimizedPagerAction")) {
	    declare id = TextLib::Split("_", Event.ControlId);
	    declare Integer row = TextLib::ToInteger(id[1]) + currentIndex;   
	    declare Integer col = TextLib::ToInteger(id[2]);   

	    if (data.existskey(row) && data[row].existskey(col)){
		entry.Value = " " ^ data[row][col];
	    }else{
		entry.Value = "";
	    }
	}
	else{
	    entry.Value = "";
	}
    }
}

if (MouseLeftButton == False) { 
    pager_firstClick = False;
    pager_moveScroll = False;
}else if(pager_moveScroll){
    pager_deltaMouseYPosition = MouseY - pager_startMouseYPosition;
    log("Moving : "^pager_deltaMouseYPosition);
}
