
<style>
	#ci-debugpanel,#ci-debugwindow {
		-moz-box-sizing: content-box;
		height: 18px;
		border: 1px solid #888888;
		position: fixed;
		bottom: 0px;
		right: 100px;
		line-height: 18px;
		padding: 5px;
		font-family: Gadget, sans-serif;
		background: #f2f5f6; /* Old browsers */
		background: -moz-linear-gradient(top,  #f2f5f6 0%, #e3eaed 37%, #c8d7dc 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2f5f6), color-stop(37%,#e3eaed), color-stop(100%,#c8d7dc)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f5f6', endColorstr='#c8d7dc',GradientType=0 ); /* IE6-9 */
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		z-index: 1000;
	}
	#ci-debugpanel > .ci-panel {
		
		border-left: 1px solid #aaa;
		padding-left: 3px;
		margin-left: 3px;
		float: left;
	}
	#ci-debugpanel > .ci-panel:nth-child(2) { border-left: none;}
	#ci-debugpanel-close { color: #999; font-size: 10px; text-align: center; width: 11px; }
	#ci-debugwindow,.ci-window{
		background: #FFFFFF;
		display: none;
		max-width: 1100px;
		position: absolute;
		right: 0;
		bottom: 30px;
		border: 1px solid #888;
		background-color: #FFF;
		height: auto;
		color: #494949;
		border: 10px solid #EFECE3;
		box-shadow: 0px 0px 5px 0px #CCC; 
		max-height: 567px;
		overflow-y: scroll;
	}
	#ci-debugwindow span { color: #222;}
	#ci-debugwindow > h3 { font-size: 16px; border: 5px solid #EFECE3; border-bottom: 1px solid #888; margin-bottom: 15px; text-transform: uppercase; background-color: #EFECE3;}
	.ci-panel.hand { cursor: pointer}
	.ci-panel > img { float: left; padding-right: 5px; position: relative; top: 2px;}
	.ci-rowdelete { border-right: 1px solid #CCC; padding-right: 4px; cursor: pointer;}
	#ci-debugwindow table { border-collapse: collapse; width: 100%;}
	#ci-debugwindow tr { background-color: #F4F3F1; font: 9pt/1.5 Consolas,monospace;}
	#ci-debugwindow th { color: #575757;border: 1px solid #CACACA; padding: 2px 4px; border-bottom: 1px solid #C1C1C1;}
	#ci-debugwindow td { background-color: #FDF5CE;padding: 3px 5px; border: 1px solid #CACACA; border-top: none;}
	#ci-debuglogo { background: url("<?php echo base_url( 'images/console/logo.png' ) ?>") no-repeat center center; width: 90px; height: 20px; float: left;}
	#ci-debugwindow .string { color: green; }
	#ci-debugwindow  .number { color: darkorange; }
	#ci-debugwindow  .boolean { color: blue; }
	#ci-debugwindow  .null { color: magenta; }
	#ci-debugwindow  .key { color: red; }
	#ci-debugwindow .more-info { color: #007700;font-weight: bold; cursor: pointer;}
	.ci-more-info-container { display: none;}

</style>
<div id="ci-debugpanel">
	<div id="ci-debugwindow"></div>
	<div id="ci-debuglogo" title="Codeigniter debugbar | autor: Pavel Vais"></div>
	<!--	<div class="ci-panel hand" onclick="debugpanel.open('ci-window-postdata')">
			post data
			<div class="ci-window" id='ci-window-postdata'>array('holt' => 'molt')</div>
		</div>-->
	<?php foreach ( $panels as $panel ): ?>
		<?php echo $panel ?>
	<?php endforeach; ?>

	<div id="ci-debugpanel-close" class="ci-panel hand" onclick="debugpanel.close()">X</div>

</div>
<script>
		debugpanel = {
			div: $('#ci-debugpanel'),
			prefix: "<?php echo $prefix ?>",
			isExists: function() {
				return debugpanel.div.length === 0 ? false : true
			},
			close: function() {
				debugpanel.div.remove();
			},
			open: function(obj)
			{
				o = $('#' + obj);
				t = $('#ci-debugwindow');
				if (t.data('ci-panelid') === obj && t.css("display") == "block")
				{
					t.hide();
					return;
				}
				t.empty().data('ci-panelid', obj)
						  .append("<h3>" + o.data("windowlabel") + "</h3>")
						  .append($('#' + obj).html())
						  .removeAttr('style')
						  .show();
				if (o.data('windowwidth') !== undefined)
					t.css('min-width', o.data('windowwidth')).css('width', "auto");

			},
			addPanel: function(name, label, heading, title, window_data, image, insert_position)
			{
				if ($('#' + debugpanel.prefix + name).length > 0)
					return;

				output = '<div title="' + title + '" class="' + debugpanel.prefix + 'panel ' + (window_data !== null ? "hand" : "") + '"';

				output += (window_data !== null ? " onclick=\"debugpanel.open('" + debugpanel.prefix + name + "')\"" : "") + ">";

				if (image !== null)
					output += '<img src="<?php echo base_url( 'images/console/' ) ?>/' + image + '" >';

				output += "<span>" + label + "</span>";

				if (window_data !== null)
				{
					output += '<div class="' + debugpanel.prefix + 'window" id="' + debugpanel.prefix + name + '" data-windowlabel="' + heading + '">';
					output += window_data;
					output += "</div>";
				}

				output += "</div>";

				if (insert_position === null || insert_position < 3)
					insert_position = 3;

				$("#" + debugpanel.prefix + "debugpanel ." + debugpanel.prefix + "panel:nth-child(" + insert_position + ")").before(output);
			},
			refreshWindow: function(panel_name)
			{
				if ($('#ci-debugwindow').data(debugpanel.prefix + 'panelid') === debugpanel.prefix + panel_name && t.css("display") == "block")
				{
					$('#ci-debugwindow').html('<h3>' + $('#' + debugpanel.prefix + panel_name).data("windowlabel") + '</h3>' + $('#' + debugpanel.prefix + panel_name).html());
				}
			},
			addTableRow: function(window_name, row_data, row_id) {
				td = "";
				for (var i = 0; i < row_data.length; i++) {
					td += "<td>" + row_data[i] + "</td>";
				}

				$tr = $('<tr/>', {
					id: row_id === undefined ? "" : row_id,
					html: td
				});

				if ($('#' + debugpanel.prefix + window_name).find("tbody tr").length > 30)
					$('#' + debugpanel.prefix + window_name).find("tbody tr:lt(" + row_data.length + ")").remove();

				$('#' + debugpanel.prefix + window_name).find("tbody").append($tr);


				debugpanel.refreshWindow(window_name);

			},
			changeTableCell: function(window_name, row_id, cell_index, cell_data) {
				$('#' + debugpanel.prefix + window_name).find("#" + row_id + " td:nth-child(" + cell_index + ")").html(cell_data);
				debugpanel.refreshWindow(window_name);
			},
			getLabel: function(panel_id)
			{
				return $('#' + debugpanel.prefix + panel_id).parent().find('span:first').text();
			},
			getTitle: function(panel_id)
			{
				return $('#' + debugpanel.prefix + panel_id).parent().attr('title');
			},
			changeLabel: function(panel_id, label) {
				$('#' + debugpanel.prefix + panel_id).parent().find('span:first').text(label);
			},
			changeTitle: function(panel_id, title) {
				$('#' + debugpanel.prefix + panel_id).parent().attr('title', title);
			},
			isPanelExists: function(panel_id)
			{
				return $('#' + debugpanel.prefix + panel_id).length > 0 ? true : false;
			},
			panelData: function(panel_id, data_name, data_value)
			{
				if (data_value === undefined)
				{
					return $('#' + debugpanel.prefix + panel_id).parent().data(data_name)
				} else {
					$('#' + debugpanel.prefix + panel_id).parent().data(data_name, data_value);
				}
			},
			removeRow: function(panel_id, row_id)
			{
				$('#' + debugpanel.prefix + panel_id).find('#' + row_id).remove();
				debugpanel.refreshWindow(panel_id);
			},
			changeRowAttr: function(panel_id, row_id, attr_name, attr_value)
			{
				$('#' + debugpanel.prefix + panel_id).find('#' + row_id).attr(attr_name, attr_value);
				debugpanel.refreshWindow(panel_id);
			}
		}

		$(document).ready(function() {
			$('#ci-debugpanel').on("click", ".more-info", function() {
				t = $(this);
				innerHtml = t.find("." + debugpanel.prefix + "more-info-container").html();

				myWindow = window.open('', '', 'width=500,height=200,titlebar=0,status=0,toolbar=0,menubar=0,location=0,scrollbars=1')
				myWindow.document.write(innerHtml);
				myWindow.focus()
			});
			/*$('#ci-debugpanel').on("click", ".shortened", function() {
			 t = $(this);
			 if (t.data('opened') !== "t")
			 {
			 te = t.text();
			 t.data('opened','t').text(t.data('value'));
			 t.data('value',te);
			 } else {
			 t.data('opened','').text(t.data('value'));
			 te = t.text();
			 t.data('value',te);
			 }
			 });*/

			$(document).ajaxSuccess(function(event, XMLHttpRequest, ajaxOptions) {
				if (debugpanel.isExists())
					showAjaxInfo(event, XMLHttpRequest, ajaxOptions);
			});
			//http://www.w3schools.com/jsref/met_win_open.asp


			$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
				if (debugpanel.isExists())
					showAjaxInfo(event, jqXHR, ajaxSettings, true);
			});


		});

		function showAjaxInfo(event, request, ajax_settings, error)
		{
			panel_id = "ajaxinfo";
			ajaxid = "id_" + event.timeStamp;
			url = ajax_settings.url;
			parameters = "<pre>" + param2html(ajax_settings.data) + "</pre>";
			answer = "<pre>" + json2hmtl(request.responseText) + "</pre>";
			tr_class = (error === undefined ? 'color: green;' : 'color: red;');
			status_msg = (error === undefined ? 'success' : 'error') + "(" + request.status + ")";
			delete_span = "<span class='" + debugpanel.prefix + "rowdelete' onclick='debugpanel.removeRow(\"" + panel_id + "\",\"" + ajaxid + "\")'>x</span>";
			stat_datacaller = Array("ajaxcallssuc", "ajaxcallserr");

			if (!debugpanel.isPanelExists("ajaxinfo"))
			{
				table = "<table><tr><th style='min-width: 120px;'>status</th><th>url</th><th>parameters</th><th>answer</th></tr>";
				table += "<tr id='" + ajaxid + "' style='" + tr_class + "'><td>" + delete_span + " " + status_msg + "</td>";
				table += "<td>" + url + "</td><td>" + parameters + "</td><td>" + answer + "</td>";
				table += "</table>";
				debugpanel.addPanel(panel_id, "1 ajax calls", 'Ajax sniffer', 'ajax calls: suc: 0 err: 0', table, "ajax.png", 1);
				debugpanel.panelData(panel_id, "ajaxcalls", 1);
				debugpanel.panelData(panel_id, stat_datacaller[(error === undefined ? 0 : 1)], 1);
				debugpanel.panelData(panel_id, stat_datacaller[(error === undefined ? 1 : 0)], 0);
				debugpanel.changeTitle(panel_id, "ajax calls: suc: " + debugpanel.panelData("ajaxinfo", stat_datacaller[0]) + " err: " + debugpanel.panelData("ajaxinfo", stat_datacaller[1]));
				$('#' + debugpanel.prefix + "ajaxinfo").data('windowwidth', 700);
			} else {
				debugpanel.panelData(panel_id, stat_datacaller[(error === undefined ? 0 : 1)], debugpanel.panelData(panel_id, stat_datacaller[(error === undefined ? 0 : 1)]) + 1);
				debugpanel.panelData(panel_id, "ajaxcalls", debugpanel.panelData("ajaxinfo", "ajaxcalls") + 1);
				debugpanel.changeLabel(panel_id, debugpanel.panelData("ajaxinfo", "ajaxcalls") + " ajax calls");
				debugpanel.changeTitle(panel_id, "ajax calls: suc: " + debugpanel.panelData("ajaxinfo", stat_datacaller[0]) + " err: " + debugpanel.panelData("ajaxinfo", stat_datacaller[1]));
				debugpanel.addTableRow(panel_id, Array(delete_span + " " + status_msg, url, parameters, answer), ajaxid);
				debugpanel.changeRowAttr(panel_id, ajaxid, 'style', tr_class);
			}

			try {
				response = jQuery.parseJSON(request.responseText);
				if (response.db_queries !== undefined)
				{

					// Query Sniffer
					if (!debugpanel.isPanelExists("queries"))
					{

					} else {
						//console.log(response.db_queries[0]);
						for (var key in response.db_queries) {

							row = response.db_queries[key];
							row.functions = "<span style='color:#BF3BA9; font-size: 10px'>ajax: " + url + "</span><br>" + row.functions;
							//console.log(row);
							debugpanel.addTableRow("queries", Array(row.functions, row.query, row.elapsed_time, row.rows), 'id_' + Math.round(100));
							debugpanel.changeLabel("queries", +(parseInt(debugpanel.getLabel("queries")) + 1) + " queries");
							debugpanel.changeTitle("queries", +(parseInt(debugpanel.getTitle("queries")) + 1) + " queries was executed");

						}


					}

				}
			} catch (e) {
			}

		}

		function param2html(string)
		{
			if (string === "" || string === undefined)
				return;

			return string.replace(/=/g, ": ").replace(/&/g, "\n");
		}

		function json2hmtl(json)
		{
			if (json === undefined)
				return json;

			if (typeof json != 'string') {
				json = JSON.stringify(json, undefined, 4);
			}
			json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
			return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
				var cls = 'number';
				if (/^"/.test(match)) {
					if (/:$/.test(match)) {
						cls = 'key';
					} else {
						cls = 'string';
					}
				} else if (/true|false/.test(match)) {
					cls = 'boolean';
				} else if (/null/.test(match)) {
					cls = 'null';
				}
				if (match.length > 20)
				{
					match_short = match.substr(0, 17) + "...";
					cls += " shortened";
				} else
					match_short = match;
				return '<span class="' + cls + '" data-value="' + match + '">' + match_short + '</span>';
			});
		}
</script>
