<?php defined('SYSPATH') or die ('No direct script access.'); ?>

<?php

    class Controller_Pagefiller_Default_Component_Link extends Controller_ACL
    {

        public $template;

        public function before()
        {
            Wi3::inst()->acl->grant("*", $this, "login"); // Everybody can access login and logout function in this controller
            Wi3::inst()->acl->grant("*", $this, "logout");
            Wi3::inst()->acl->grant("admin", $this); // Admin role can access every function in this controller
            Wi3::inst()->acl->check($this);
        }

        public function login()
        {

        }

        public static function view($viewname)
        {
            // Make this component view extend the base template, with their locations set to the component folders
            $componenturl = Wi3::inst()->urlof->pagefillerfiles("default") . "components/link/";
            $componentpath = Wi3::inst()->pathof->pagefiller("default") . "components/link/";
            $componentbaseview = Wi3_Baseview::instance('imagecomponentbaseview', array(
                'javascript_url' => $componenturl.'static/javascript/',
                'javascript_path' => $componentpath.'static/javascript/',
                'css_url' => $componenturl.'static/css/',
                'css_path' => $componentpath.'static/css/'
            ));
            $componentview = View::factory()->set("this", $componentbaseview);
            $componentview->set_filepath($componentpath.'views/'.$viewname.EXT); // set_filepath sets a complete filename on the View
            return $componentview;
        }

        public function action_startEdit()
        {
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            $html = $this->view("edit")->set("field", $field)->render();
            echo json_encode(
                Array(
                    "dom" => Array(
                        "fill" => Array(
                            "div[type=popuphtml]" => $html
                        )
                    ),
                    "scriptsafter" => Array(
                        "0" => "wi3.pagefillers.default.edittoolbar.showPopup();",
                        "1" => "$('.pagefiller_default_component_link_edit_tabs').tabs();"
                    )
                )
            );
        }

        public function action_edit()
        {
            // Load field, and the image-date field that connects to it
            $fieldid = $_POST["fieldid"];
            $field = Wi3::inst()->model->factory("site_field")->set("id", $fieldid)->load();
            // Update data field with image-id
            $data = Wi3::inst()->model->factory("site_array")->setref($field)->setname("data")->load();
            if(isset($_POST["url"])) {
				$data->url = $_POST["url"];
				$data->destinationtype = "url";
			} else if(isset($_POST["fileid"])) {
				$data->fileid = $_POST["fileid"];
				$data->destinationtype = "file";
			} else if(isset($_POST["pageid"])) {
                $data->pageid = $_POST["pageid"];
                $data->destinationtype = "page";
            }
            $data->update();

            $url = "";
            $destinationtype = $data->destinationtype;
            // get proper URL
            if ($destinationtype == "url")
            {
                $url = $data->url;
            }
            else if ($destinationtype == "file")
            {
                $fileid = $data->fileid;
                $file = Wi3::inst()->model->factory("site_file")->set("id", $fileid)->load();
                if ($file->loaded())
                {
                    $url = Wi3::inst()->urlof->file($file);
                }
                else
                {
                    $url = "";
                }
            }
            else if ($destinationtype == "page")
            {
                $pageid = $data->pageid;
                $page = Wi3::inst()->model->factory("site_page")->set("id", $pageid)->load();
                if ($page->loaded())
                {
                    $url = Wi3::inst()->urlof->page($page->slug);
                }
                else
                {
                    $url = "";
                }
            }

            echo json_encode(
                Array(
                    "scriptsbefore" => Array(
                        "0" => "$('[type=field][fieldid=" . $fieldid . "] [type=fieldcontent] > a').attr('href', '" . $url . "');",
                        "1" => "wi3.pagefillers.default.edittoolbar.hidePopup();"
                    )
                )
            );
        }

    }

?>
