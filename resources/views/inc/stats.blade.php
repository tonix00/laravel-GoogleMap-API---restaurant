<div id="statsWindow" title="Statistics">
    <input type='hidden' id='statIndex' value='' />
    <div id='statRestaurantInfo'></div>
    <div id='statRestaurantSurvey'>
        <div class="form-group">
            <fieldset>
                <legend>A short survey.</legend>
                Hi! Please answer the following questions below.<br />
                <table id='tbSurvey'>
                    <tr>
                        <td><label for="visit_date">When did you visit them?</label></td>
                        <td><input type='date' name='visit_date' id="visit_date" /></td>
                    </tr>
                    <tr>
                        <td><label for="stat_specific_food">Specialty or favorite food ordered from them?</label></td>
                        <td><input type='text' name='specific_food' id='stat_specific_food' /></td>
                    </tr>
                </table>
                <br />
                <button class="btn btn-primary" onclick="saveRealStatInfo()" id='saveStatbutton'>Save</button> 
                <button class="btn btn-primary" onclick="showRealStatInfo()">Skip</button>
            </fieldset>
        </div>
    </div>
    <div id='realStatInfo'>
        <table>
            <tr>
                <td id='cell1'>
                    <div id="curve_chart" style="width:510px; height: 340px;padding:0px;margin:0px; border: 1px solid red"></div>
                </td>
                <td id='cell2' valign="top">
                    <div id='statFood'>
                    <fieldset>
                        <legend><small>Foods</small></legend>
                        <ul id='ulFoodList'></ul>
                    </fieldset>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>