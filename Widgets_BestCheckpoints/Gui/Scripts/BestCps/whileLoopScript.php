foreach (Player in Players) {

    //If first checkpoint time or new checkpoint time
    declare playerCp for Player = -1;

    if(playerCp != Player.CurRace.Checkpoints.count) {
	//Update the current checkpoint of this user
	playerCp = Player.CurRace.Checkpoints.count;
	
	declare curCp = Player.CurRace.Checkpoints.count;
	declare cpIndex = (curCp % totalCp)-1;
	declare Integer lastCpIndex = totalCp - 1;
	declare time = 0;

	 if( curCp > totalCp){
	    time = Player.CurRace.Checkpoints[curCp-1] - Player.CurRace.Checkpoints[lastCpIndex];
	} else if(curCp > 0){
	    time = Player.CurRace.Checkpoints[curCp-1];
	}

	//Check if better
	if(cpIndex >= 0 && cpIndex < maxCpIndex && (cpTimes[cpIndex] > time || cpTimes[cpIndex] == 0)){
	    needUpdate = True;
	    cpTimes[cpIndex] = time;

	    declare nickLabel = (Page.GetFirstChild("CpNick_"^cpIndex) as CMlLabel);
	    declare timeLabel = (Page.GetFirstChild("CpTime"^cpIndex) as CMlLabel);
	    declare background = (Page.GetFirstChild("Bg"^cpIndex) as CMlQuad);

	    if(nickLabel != Null){		
		nickLabel.SetText(Player.Name);
		timeLabel.SetText("$ff0" ^ (cpIndex + 1 ) ^ " $fff" ^ TimeToText(cpTimes[cpIndex]) );
		background.Show();
	    }
	}
    }
}
