{extends 'page.tpl'}

{block content}

<h2>
    Hello from the font controller for the module <i>My module</i>
</h2>
    <form action="{$smarty.server.PHP_SELF}?fc=module&module=mymodule&controller=message&id_lang=1"
          method="post"

    >
        <label for="">
            <input type="text" name="name" id="name" class="form-control">
        </label>

        <label for="">
            <input type="submit" name="submit" id="submit" class="form-control">
        </label>


    </form>
{/block}