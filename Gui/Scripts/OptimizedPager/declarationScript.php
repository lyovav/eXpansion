
//Declaring all variables needed during all run time

declare Real itemSizeY = 6.0;
declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);
declare CMlQuad ScrollUp <=> (Page.GetFirstChild("ScrollUp") as CMlQuad);
declare CMlQuad ScrollDown <=> (Page.GetFirstChild("ScrollDown") as CMlQuad);
declare Real disabledOpacity = 0.2;

declare CMlEntry entry <=> (Page.GetFirstChild("entry") as CMlEntry);

declare Real pager_scrollYOriginPosition;                    
declare Real pager_mouseYPosition;                    
declare Real pager_deltaMouseYPosition;                    
declare Real pager_startMouseYPosition;                    
declare pager_moveScroll = False;
declare pager_firstClick = False;

declare Real ScrollBarHeight = ScrollBar.Size.Y;
declare Real ScrollBgHeight = ScrollBg.Size.Y;

declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
declare Real pagerStopPosition = pagerStartPos - ScrollBgHeight + ScrollBarHeight;

declare Text[][Integer] textData = <?php echo $this->items;  ?>;
declare Text[][Integer] data <?php echo $this->data;  ?>;

declare Integer rowsPerPage = <?php echo $this->rowPerPage;  ?>;

declare Integer itemsPerRow = <?php echo $this->itemsPerRow; ?>;
declare Integer totalRows = <?php echo $this->totalRows; ?>;

declare Integer currentIndex = 0;
declare Integer oldIndex = 0;
declare Integer maxIndex = totalRows - rowsPerPage;

declare CMlLabel[Integer][Integer] labels;

//Initializing the script
             
for(i, 0, rowsPerPage) {

    labels[i] = CMlLabel[Integer];

    for(r, 0, itemsPerRow-1) {
	declare CMlLabel item <=> (Page.GetFirstChild("column_"^i^"_"^r) as CMlLabel);
	labels[i][r] = item;
		
        if (item != Null && textData.existskey(i)) {                    
	    item.SetText(textData[i][r]);    
	}        
    }
}

if (textData.count <= rowsPerPage) {
    ScrollBar.Hide();
    ScrollBg.Opacity = disabledOpacity;    
    ScrollDown.Opacity = disabledOpacity;    
    ScrollUp.Opacity = disabledOpacity;        
}