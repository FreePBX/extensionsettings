function OnOffFormat(value, row, idx)
{
    var html = "";
    if (value === true)
    {
        html = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
    }
    else
    {
        html = '<i class="fa fa-square-o" aria-hidden="true"></i>';
    }
	return html;
}

function ExtensionsFormat(value, row, idx)
{
    var html = "";
    html += sprintf('<a href="%s" class="btn btn-primary btn-sm" title="%s"><i class="fa fa-pencil" aria-hidden="true"></i></a>', row['edit_url'], _("Edit"));
    if (value == row['description'])
    {
        html += value;
    }
    else 
    {
        html += sprintf('%s (%s)', value, row['description']);
    }
    
    return html;
}

function FMListFormat(value, row, idx)
{
    var html = "";
    $.each(value, function(index, val)
    {
        html += sprintf('<span class="label label-default label-list">%s</span>', val);
    });
    return html;
}


$("#set_table").bootstrapTable({
    onColumnSwitch: onColumnSwitch
});

// Global varible for the colspan default values.
var colspanSizeGlobal = {};

// Resize colspan
function onColumnSwitch(field, checked)
{
    var options = $("#set_table").bootstrapTable("getOptions");
    var nCol    = 0;

    $("#set_table thead tr:first-child th").each(function()
    {
        var colspan = $(this).attr("colspan");
        if (colspan == undefined)
        {
            // If not colspan continue, ignorer column
            return true;
        }
        else
        {
            // convert string to integer
            colspan = parseInt(colspan, 10);
        }

        // Check if colspan is set int the global colspansize
        var colspanSize = colspanSizeGlobal[(this).cellIndex];
        if (colspanSize == undefined)
        {
            colspanSizeGlobal[(this).cellIndex] = colspan;
            colspanSize = colspan;
        }
        
        // Set size column group and detect which columns are visible
        var stopCol = nCol + colspanSize;
        for (var i = nCol; i < stopCol; i++)
        {
            var column = options.columns[1][i];
            if (column == undefined) { continue; }

            colspanSize -= !column.visible ? 1 : 0;
        }

        // Resize column group
        $(this).attr("colspan", colspanSize);
        nCol = stopCol;

        // Show or hide column group
        if (colspanSize > 0)
        {
            $(this).show();
        }
        else
        {
            $(this).hide();
        }
    });
}

// Remove column from show/hide columns button
$('#set_table').on('post-body.bs.table', function()
{
    $('th.remove-from-show-columns').each(function()
    {
        var dataField = $(this).attr('data-field');
        if (dataField == undefined || dataField == null || dataField == "")
        {
            return true;
        }
        $('ul.dropdown-menu input[data-field="' + dataField + '"]').closest('li').remove();
    });
});