
foreach (Player in Players) {

    declare <?php echo $this->varName ?> for Player = -1;

    if (<?php echo $this->varName ?> != Player.CurRace.Checkpoints.count) {	
	
	//Update the current checkpoint of this user
	declare curCp = Player.CurRace.Checkpoints.count -1 ;	
	<?php echo $this->varName ?> = curCp+1;
	
	
	//Check if valid checkpoint
	if(curCp >= 0){
	    if((curCp+1) == totalCp*nbLaps && givePoints){
		nbFinish += 1;
	    }
	    needUpdate = True;
	    //Check if max Checkpoint
	    if(maxCp <= curCp){
		maxCp = curCp+1;
	    }

	    declare <?php echo $this->varName ?>_cpPosition for Player = -1;
	    declare newCpPosition = 0;
	    
	    playersTeam[Player.Login] = Player.RequestedClan;
	    
	    //Register Checkpoint time
	    if(!playerTimes.existskey(curCp)){
		//Is it first player throught this checkpoint?
		playerTimes[curCp] = Integer[Text];
		playerNickNames[curCp] = Text[Text];
	    }
	    playerTimes[curCp][Player.Login] = Player.CurRace.Checkpoints[curCp];
	    playerNickNames[curCp][Player.Login] = Player.Name;
	    
	    //Remove from older checkpoint
	    if(curCp > 0){
		if(playerTimes.existskey(curCp-1)){
		    playerTimes[curCp-1].removekey(Player.Login);
		    playerNickNames[curCp-1].removekey(Player.Login);
		}
	    }
	}
    }
}

if(!needUpdate){
    lastUpdateTime = Now;
}
if (needUpdate && (((Now - lastUpdateTime) > 500 && exp_widgetVisibleBuffered && exp_widgetLayersBuffered == activeLayer) || exp_widgetVisibilityChanged)) {
    lastUpdateTime = Now;

    needUpdate = False;
    
    log ("update: " ^ id);
    
    declare i = 1;
    declare nbRec = 1;
    declare showed = False;

    declare myRank = -1;
    declare start = -1;
    declare end = -1;
    declare recCount = -1;
    
    i = 1;
    
    declare cpIndex = maxCp -1;
    while(cpIndex >= 0){
	if(playerTimes.existskey(cpIndex)){
	    declare Players2 = playerTimes[cpIndex];
	    foreach(p => Score in Players2){
		if (LocalUser != Null) {
		    if (playerNickNames[cpIndex][p] == LocalUser.Name) {
			myRank = i;
		    }
		}
		i += 1;
		if(myRank != -1 && i > myRank + (nbFields - nbFirstFields)){
		    break;
		}
	    }
	     if(myRank != -1 && i > myRank + (nbFields - nbFirstFields)){
		break;
	    }
	}
	cpIndex -= 1;
    }
    recCount = i;
    
    if(LocalUser.RequestsSpectate && givePoints){
	myRank = nbFinish;
    }

    if (myRank != -1) {
	start = myRank - ((nbFields - nbFirstFields) / 2);

	if (start <= nbFirstFields) {
	    start = nbFirstFields;
	    end = start + (nbFields - nbFirstFields);
	} else {
	    end = start + (nbFields - nbFirstFields);
	    if (end > recCount) {
		end = recCount;
		start = end - (nbFields - nbFirstFields);
	    }
	}
    } else {
	start = nbFirstFields;
	end = start + (nbFields - nbFirstFields);
    }

    i = 1;
    nbRec = 1;
    declare firstOfCp = True;
    cpIndex = maxCp -1;
    
    declare teamRedScore = 0;
    declare teamBlueScore = 0;
    declare total = recCount;
    if(total > maxPoint){
	total = maxPoint;
    }
    
    while(cpIndex >= 0){
	declare bestCp = 0;
	if(playerTimes.existskey(cpIndex)){
	    declare Players2 = playerTimes[cpIndex];
	    foreach(p => Score in Players2){
		if ((nbRec <= nbFirstFields || (nbRec > start && nbRec <= end) ) && i <= nbFields) {

		    declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
		    declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
		    declare highliteQuad = (Page.GetFirstChild("RecBg_"^i) as CMlQuad);
		    declare rank = (Page.GetFirstChild("RecRank_"^i) as CMlLabel);
		   
		    
		    /*if (highliteQuad != Null) {
			if (playersOnServer.existskey(Login) && i != myRank) {
			    highliteQuad.Show();
			} else {
			    highliteQuad.Hide();
			}
		    }*/

		    if (playerNickNames[cpIndex][p] == LocalUser.Name) {
			showed = True;
		    }

		    nickLabel.SetText(playerNickNames[cpIndex][p]);
		    
		    if(isTeam){
			if(playersTeam[p] == 1){
			    timeLabel.SetText("$00F"^TimeToText(Score));
			    rank.SetText("$00F"^i);
			    declare point = total - i;
			    if(point < 0){
				point = 0;
			    }
			    teamBlueScore +=  point;
			}else{
			    timeLabel.SetText("$F00"^TimeToText(Score));
			    rank.SetText("$F00"^i);
			    declare point = total - i;
			    if(point < 0){
				point = 0;
			    }
			    teamRedScore += point;
			}
		    }else{
			timeLabel.SetText(TimeToText(Score));
			rank.SetText(""^i);
		    }
		    
		    declare labelInfo1 = (Page.GetFirstChild("RecInfo1_"^i) as CMlLabel);
		    declare labelInfo2 = (Page.GetFirstChild("RecInfo2_"^i) as CMlLabel);
		    
		    if(nbRec == 1){
			labelInfo1.SetText("   -"^"-:"^"-"^"-"^"."^"-"^"-"^"-");
			labelInfo2.SetText("   -"^"-:"^"-"^"-"^"."^"-"^"-"^"-");
		    }else if(firstOfCp){
			labelInfo1.SetText("$00F"^"-"^"-:"^"-"^"-"^"."^"-"^"-"^"-");
			labelInfo2.SetText("$00F"^"-"^"-:"^"-"^"-"^"."^"-"^"-"^"-");
		    }else{
			declare diff = Score - bestCp;
			if(lastTimeDiff > diff && playerNickNames[cpIndex][p] == LocalUser.Name){
			    labelInfo1.SetText("$F00+"^TimeToText(Score - bestCp));
			    labelInfo2.SetText("$F00+"^TimeToText(Score - bestCp));
			}else if(lastTimeDiff < diff && playerNickNames[cpIndex][p] == LocalUser.Name){
			    labelInfo1.SetText("$0F0+"^TimeToText(Score - bestCp));
			    labelInfo2.SetText("$0F0+"^TimeToText(Score - bestCp));
			}else{
			    labelInfo1.SetText("+"^TimeToText(Score - bestCp));
			    labelInfo2.SetText("+"^TimeToText(Score - bestCp));
			}
			if(showed){
			    lastTimeDiff = diff;
			}
		    }



		    declare labelCp1 = (Page.GetFirstChild("RecCp1_"^i) as CMlLabel);
		    declare labelCp2 = (Page.GetFirstChild("RecCp2_"^i) as CMlLabel);
		    
		    if((cpIndex+1) == totalCp*nbLaps && givePoints){
			if(isTeam){
			   declare point = total  - i;
			    if(point < 0){
				point = 0;
			    }
			    teamRedScore += point;
			    labelCp1.SetText("$2A2"^point^" pts");
			    labelCp2.SetText("$2A2"^point^" pts");
			}else{
			     if(points.existskey(nbRec-1)){
				labelCp1.SetText("$2A2"^points[nbRec-1]^" pts");
				labelCp2.SetText("$2A2"^points[nbRec-1]^" pts");
			    }else{
				labelCp1.SetText("$2A20P");
				labelCp2.SetText("$2A20P");
			    }
			}
		    }else{
			if(nbRec == 1){
			     declare lap = 0;
			     lap = cpIndex/totalCp;
			     if(lap > 0 && isLaps){
				labelCp1.SetText("Lap"^lap);
				labelCp2.SetText("Lap"^lap);
			     }else{
				labelCp1.SetText("Cp"^(cpIndex+1));
				labelCp2.SetText("Cp"^(cpIndex+1));
			     }
			}else{
			    declare diff = maxCp - cpIndex - 1;
			    if(diff > 0){
				labelCp1.SetText("+"^diff^" cp");
				labelCp2.SetText("+"^diff^" cp");
			    }else{
				labelCp1.SetText("");
				labelCp2.SetText("");
			    }
			}
		    }
		    
		    declare bg = (Page.GetFirstChild("RecBgBlink_"^i) as CMlQuad);

		    if(playerNickNames[cpIndex][p] == LocalUser.Name){
			    highliteQuad.Hide();    
			    bg.Show();
		    }else{
			    bg.Hide();
		    }


		    i += 1;
		}
		bestCp = Score;
		nbRec += 1;
		firstOfCp = False;
	    }
	    firstOfCp = True;
	}
	cpIndex -= 1;
    }
    
    if(isTeam){
	declare blueLabel = (Page.GetFirstChild("bluePoints") as CMlLabel);
	blueLabel.SetText(""^teamBlueScore);
	
	declare redLabel = (Page.GetFirstChild("redPoints") as CMlLabel);
	redLabel.SetText(""^teamRedScore);
    }
}

foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "setLayer") {    
	if (eXp_widgetLayers[version][id][gameMode] == "normal") {	
	    eXp_widgetLayers[version][id][gameMode] = "scorestable";	    
	    exp_needToCheckPersistentVars = True;
	} else {
    	    eXp_widgetLayers[version][id][gameMode] = "normal";	    
	    exp_needToCheckPersistentVars = True;
	}
    }
}