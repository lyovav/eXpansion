<?php       

    $deltaX = "DeltaPos.X = MouseX - lastMouseX;";
    $deltaY = "DeltaPos.Y = MouseY - lastMouseY;";

    if ($this->axisDisabled == "x")
        $deltaX = "";
    if ($this->axisDisabled == "y")
        $deltaY = "";

?>

if(!disablePersonalHud){
    //Check if persistent variables needs to be checked, or first loop
    if(exp_needToCheckPersistentVars || !eXp_firstPersistentCheckDone) {        
	exp_multipleCheckCount += 1;
	eXp_firstPersistentCheckDone = True;

	if(exp_multipleCheckCount > 10) {
	    exp_needToCheckPersistentVars = False;
	    exp_multipleCheckCount = 0;
	}

	    if (!eXp_widgetVisible.existskey(version) ) {
		    eXp_widgetVisible[version] = Boolean[Text][Text];
	    }

	    if ( !eXp_widgetVisible[version].existskey(id) || forceReset) {
		    eXp_widgetVisible[version][id] = Boolean[Text];
	    }

	    if ( !eXp_widgetVisible[version][id].existskey(gameMode) ) {
		    eXp_widgetVisible[version][id][gameMode] = <?php echo $win->getWidgetVisible(); ?>;
	    }

	    if (!eXp_widgetLayers.existskey(version) ) {
		    eXp_widgetLayers[version] = Text[Text][Text];
	    }

	    if (!eXp_widgetLayers[version].existskey(id) || forceReset) { 
		    eXp_widgetLayers[version][id] = Text[Text];
	    }

	    if (!eXp_widgetLayers[version][id].existskey(gameMode)) { 
		    eXp_widgetLayers[version][id][gameMode] = activeLayer; 
	    }

	    if (!eXp_widgetLastPos.existskey(version)) {
		    eXp_widgetLastPos[version] = Vec3[Text][Text];
	    }

	    if (!eXp_widgetLastPos[version].existskey(id) || forceReset) {
		    eXp_widgetLastPos[version][id] = Vec3[Text];
	    }
	    if (!eXp_widgetLastPos[version][id].existskey(gameMode) ) {
		    eXp_widgetLastPos[version][id][gameMode] = < <?php echo $this->getNumber($win->getPosX()) ?>, <?php echo $this->getNumber($win->getPosY()) ?>, 0.0>;
	    }

	    if (!eXp_widgetLastPosRel.existskey(version)) {
		    eXp_widgetLastPosRel[version] = Vec3[Text][Text];
	    } 

	    if (!eXp_widgetLastPosRel[version].existskey(id) || forceReset) {
		eXp_widgetLastPosRel[version][id] = Vec3[Text];
	    }

	    if (!eXp_widgetLastPosRel[version][id].existskey(gameMode)) {
		    eXp_widgetLastPosRel[version][id][gameMode] = < <?php echo $this->getNumber($win->getPosX()) ?>, <?php echo $this->getNumber($win->getPosY()) ?>, 0.0>;
	    }

	 if (eXp_widgetVisible[version][id][gameMode] == True && eXp_widgetLayers[version][id][gameMode] == visibleLayerInit && exp_widgetCurrentVisible != eXp_widgetVisible[version][id][gameMode]) {
	    Window.Show();
	    exp_widgetCurrentVisible = True;
	} else if(exp_widgetCurrentVisible != eXp_widgetVisible[version][id][gameMode] || eXp_widgetLayers[version][id][gameMode] != activeLayer) {
	    Window.Hide();
	    exp_widgetCurrentVisible = False;
	}

	if (exp_enableHudMove == True) {
		quad.Show();
	}else {
		quad.Hide();
	}

	exp_widgetLayersBuffered = eXp_widgetLayers[version][id][gameMode];
	exp_widgetVisibleBuffered = eXp_widgetVisible[version][id][gameMode];

    } else {
	exp_multipleCheckCount = 0;
    }

    if (InputPlayer == Null) {
	yield;
	continue;
    }
    if(PageIsVisible == False){
	yield;
	continue;
    }

    if (exp_enableHudMove == True && MouseLeftButton == True) {
	    foreach (Event in PendingEvents) {
		    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "enableMove")  {
			    lastMouseX = MouseX;
			    lastMouseY = MouseY;
			    MoveWindow = True;
		    }

	    }
    }else {
	    MoveWindow = False;
    }
    foreach (Event in PendingEvents) {
	if (Event.Type == CMlEvent::Type::MouseOver ) {
	    if(Event.Control != Null ){
	       element = Event.Control;
	       element.RelativeScale = 1.1;	   
	    }	
	}
	else {
	      if (element != Null){ 	   	    
		element.RelativeScale = 1.0;	  
	      }
	}
    }		  

    if (MoveWindow) {
	    <?php echo $deltaX ?>
	    <?php echo $deltaY ?>
	    LastDelta += DeltaPos;
	    LastDelta.Z = 3.0;
	    Window.RelativePosition = LastDelta;
	    eXp_widgetLastPos[version][id][gameMode] = Window.AbsolutePosition;
	    eXp_widgetLastPosRel[version][id][gameMode] = Window.RelativePosition;

	    lastMouseX = MouseX;
	    lastMouseY = MouseY;
    }

}
