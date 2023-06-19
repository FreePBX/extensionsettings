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