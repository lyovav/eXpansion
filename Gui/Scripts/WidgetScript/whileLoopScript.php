<?php       

    $deltaX = "DeltaPos.X = MouseX - lastMouseX;";
    $deltaY = "DeltaPos.Y = MouseY - lastMouseY;";

    if ($this->axisDisabled == "x")
        $deltaX = "";
    if ($this->axisDisabled == "y")
        $deltaY = "";

?>
if (!exp_widgetVisible.existskey(version) ) {
	exp_widgetVisible[version] = Boolean[Text];
}
if (!exp_widgetVisible[version].existskey(id)) {
	exp_widgetVisible[version][id] = True;
}

if (exp_widgetVisible[version][id] == True) {
	Window.Show();
} else {
	Window.Hide();
}

if (exp_enableHudMove == True) {
	quad.Show();
}else {
	quad.Hide();
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

if (MoveWindow) {
	<?= $deltaX ?>
	<?= $deltaY ?>
	LastDelta += DeltaPos;
	LastDelta.Z = 3.0;
	Window.RelativePosition = LastDelta;
	exp_widgetLastPos[version][id] = Window.AbsolutePosition;
	exp_widgetLastPosRel[version][id] = Window.RelativePosition;

	lastMouseX = MouseX;
	lastMouseY = MouseY;
}