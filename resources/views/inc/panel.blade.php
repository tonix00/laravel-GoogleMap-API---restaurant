<div class="panel-body" id='panelContent'></div>
<div class="panel-body" id="direction-panel">
    <div class="well" id="direction-restaurant"></div>
    <a href="#" onclick="directionBack()">back</a>
</div>
<div class="panel-body" id='specificFoodContent'>
    <div class="form-group" id="specificFoodBody">
        <label for="searchFood">What food you want to eat?</label>
        <div class="input-group">
            <input type="text" class="form-control" id="searchFood" placeholder="Specialty Food :)" aria-describedby="sizing-addon2" required />
        </div>
        <br>
        <button type="submit" class="btn btn-default" onclick="searchBySpecificFood(this)">Search</button>
    </div>
    <div id="searchFoodResult"></div>
    
</div>
<div class="panel-body" id='byRadiusContent'>
    <div class="form-group" id="radiusBody">
        <label for="placesRadius">Select location:</label>
        <select class="form-control" id="placesRadius"></select>
        <label for="radius">Radius:</label>
        <select class="form-control" id="radius"></select>
        <br>
        <button type="button" class="btn btn-default" onclick='drawCircle()'>Draw Circle</button>
    </div>
    <div id="searchByRadiusResult"></div>
</div>
