$(function () {

    if($('.site_url').length)
    {

        var site_url = $('.site_url').val();

    /**
     * Start Categories Js
     */

        $('body').on('change','.select_parent_cat',function () {

            var this_element = $(this);
            var selected_cat = this_element.val();
            selected_cat = parseInt(selected_cat);
            if($('.parent_id').length)
            {
                $('.parent_id').val(selected_cat);
            }
            else if($('.pro_cat_id').length)
            {
                $('.pro_cat_id').val(selected_cat);
            }

            var parent_div_class = this_element.parents('childs_div');
            if(typeof parent_div_class != "undefined")
            {
                // console.log($('.from_parent_'+selected_cat+' + div'));
                // console.log($('.from_parent_'+selected_cat+' + div').length);

                $.each($('.from_parent_'+selected_cat+' + div'),function (ind,val) {
                    $(this).remove();
                });
            }


            var first_option_val = this_element.find('option').eq(0).val();

            if(selected_cat > 0 && (first_option_val != selected_cat))
            {

                // get this parent childs
                $.ajax({
                    url : site_url + 'ajax/get_parent_cat_childs.php',
                    type : 'POST',
                    data : {'parent_id':selected_cat},
                    success : function (data) {

                        var return_data = JSON.parse(data);
                        if(typeof return_data != 'undefined' && typeof return_data.html_data != 'undefined')
                        {

                            if(this_element.hasClass('parent_select'))
                            {
                                $('.get_child_cats').html(return_data.html_data);
                            }
                            else{
                                $('.get_child_cats').append(return_data.html_data);
                            }

                        }

                    }
                });
            }
            else{

                if(selected_cat == 0)
                {
                    $('.get_child_cats').html('');
                }

            }

            return false;
        });


        $('body').on('click','.remove_cat',function () {

            var confirm_msg = confirm("Are You Sure ?");
            if(confirm_msg)
            {

                var this_element = $(this);
                var cat_id = this_element.attr('data-cat_id');

                if(typeof cat_id != "undefined" && cat_id > 0)
                {
                    cat_id = parseInt(cat_id);

                    $.ajax({
                        url : site_url + 'ajax/remove_cat.php',
                        type : 'POST',
                        data : {'cat_id':cat_id},
                        success : function (data) {

                            var return_data = JSON.parse(data);
                            if(typeof return_data != 'undefined'
                                && typeof return_data.status != 'undefined'
                                && return_data.status == "success")
                            {
                                window.location.href = location.href;
                            }
                            else{
                                if(
                                    typeof return_data.status != 'undefined'
                                    && return_data.status == "error"){
                                    alert(return_data.msg);
                                }
                            }

                        }
                    });

                }

            }

            return false;
        });


        $('body').on('click','.edit_cat',function () {

            var this_element = $(this);
            var cat_id = this_element.attr('data-cat_id');
            var cat_name = this_element.attr('data-cat_name');
            var parent_id = this_element.attr('data-parent_id');
            var parent_cat_name = this_element.attr('data-parent_cat_name');

            if(typeof cat_id != "undefined" && typeof cat_name != "undefined"
                && cat_id > 0 && cat_name != ""
                && typeof parent_cat_name != "undefined"
                && typeof parent_id != "undefined")
            {
                cat_id = parseInt(cat_id);
                $('.cat_id').val(cat_id);
                $('.get_child_cats').html('');
                $('.cat_name').val(cat_name);
                $('.parent_id').val(parent_id);

                $('.select_parent_cat').attr("disabled","disabled");
                $('.manage_edit_cat').removeClass('hide_div');
                $('.manage_edit_cat').find('b').html(parent_cat_name);
            }

            return false;
        });

        $('body').on('change','.change_parent',function () {

            if($(this).is(":checked"))
            {
                $('.select_parent_cat').removeAttr("disabled");
                $('.select_parent_cat option').eq(0).attr("selected","selected");
            }
            else{
                $('.select_parent_cat').attr("disabled","disabled");
            }

            return false;
        });


        $('body').on('change','.add_new_cat_btn',function () {

            if(!$('.manage_edit_cat').hasClass('hide_div'))
            {
                $('.manage_edit_cat').addClass('hide_div');
            }

            $('.get_child_cats').html('');
            $('.cat_name').val('');

            return false;
        });

    /**
     * End Categories Js
     */


    /**
     * Start Products Js
     */


        $('body').on('click','.remove_pro',function () {

        var confirm_msg = confirm("Are You Sure ?");
        if(confirm_msg)
        {

            var this_element = $(this);
            var pro_id = this_element.attr('data-pro_id');

            if(typeof pro_id != "undefined" && pro_id > 0)
            {
                pro_id = parseInt(pro_id);

                $.ajax({
                    url : site_url + 'ajax/remove_pro.php',
                    type : 'POST',
                    data : {'pro_id':pro_id},
                    success : function (data) {

                        var return_data = JSON.parse(data);
                        if(typeof return_data != 'undefined'
                            && typeof return_data.status != 'undefined'
                            && return_data.status == "success")
                        {
                            window.location.href = location.href;
                        }
                        else{
                            if(
                                typeof return_data.status != 'undefined'
                                && return_data.status == "error"){
                                alert(return_data.msg);
                            }
                        }

                    }
                });

            }

        }

        return false;
    });


        $('body').on('click','.edit_pro',function () {

            var this_element = $(this);
            var pro_id = this_element.attr('data-pro_id');
            var pro_name = this_element.attr('data-pro_name');
            var cat_id = this_element.attr('data-cat_id');
            var cat_name = this_element.attr('data-cat_name');

            if(typeof pro_id != "undefined" && typeof pro_name != "undefined"
                && pro_id > 0 && pro_name != ""
                && typeof cat_name != "undefined"
                && typeof cat_id != "undefined" && cat_id > 0)
            {
                pro_id = parseInt(pro_id);
                cat_id = parseInt(cat_id);
                $('.pro_id').val(pro_id);
                $('.get_child_cats').html('');
                $('.pro_name').val(pro_name);
                $('.pro_cat_id').val(cat_id);

                $('.select_parent_cat').attr("disabled","disabled");
                $('.manage_edit_pro').removeClass('hide_div');
                $('.manage_edit_pro').find('b').html(cat_name);
            }

            return false;
        });

        $('body').on('change','.change_parent',function () {

            if($(this).is(":checked"))
            {
                $('.select_parent_cat').removeAttr("disabled");
                $('.select_parent_cat option').eq(0).attr("selected","selected");
            }
            else{
                $('.select_parent_cat').attr("disabled","disabled");
            }

            return false;
        });


        $('body').on('change','.add_new_pro_btn',function () {

            if(!$('.manage_edit_pro').hasClass('hide_div'))
            {
                $('.manage_edit_pro').addClass('hide_div');
            }

            $('.get_child_cats').html('');
            $('.pro_name').val('');

            return false;
        });

    /**
     * End Products Js
     */


    }


});