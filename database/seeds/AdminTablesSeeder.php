<?php

namespace Database\Seeds;

use Dcat\Admin\Models;
use Illuminate\Database\Seeder;
use DB;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        Models\Menu::truncate();
        Models\Menu::insert(
            [
                [
                    "id" => 1,
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "Index",
                    "icon" => "feather icon-bar-chart-2",
                    "uri" => "/",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 2,
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "Admin",
                    "icon" => "feather icon-settings",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => "2021-04-08 22:43:38"
                ],
                [
                    "id" => 3,
                    "parent_id" => 2,
                    "order" => 3,
                    "title" => "Users",
                    "icon" => "",
                    "uri" => "auth/users",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 4,
                    "parent_id" => 2,
                    "order" => 4,
                    "title" => "Roles",
                    "icon" => "",
                    "uri" => "auth/roles",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 5,
                    "parent_id" => 2,
                    "order" => 5,
                    "title" => "Permission",
                    "icon" => "",
                    "uri" => "auth/permissions",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 6,
                    "parent_id" => 2,
                    "order" => 6,
                    "title" => "Menu",
                    "icon" => "",
                    "uri" => "auth/menu",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 7,
                    "parent_id" => 2,
                    "order" => 7,
                    "title" => "Extensions",
                    "icon" => "",
                    "uri" => "auth/extensions",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 8,
                    "parent_id" => 0,
                    "order" => 8,
                    "title" => "????????????",
                    "icon" => "fa-users",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:42:05",
                    "updated_at" => "2021-04-08 21:45:33"
                ],
                [
                    "id" => 9,
                    "parent_id" => 8,
                    "order" => 9,
                    "title" => "????????????",
                    "icon" => "fa-user",
                    "uri" => "users",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 21:47:21",
                    "updated_at" => "2021-04-08 21:47:21"
                ],
                [
                    "id" => 10,
                    "parent_id" => 0,
                    "order" => 10,
                    "title" => "????????????",
                    "icon" => "fa-align-justify",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-08 22:34:03",
                    "updated_at" => "2021-04-09 14:09:32"
                ],
                [
                    "id" => 11,
                    "parent_id" => 10,
                    "order" => 11,
                    "title" => "????????????",
                    "icon" => "fa-mobile-phone",
                    "uri" => "/products",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-04-09 14:11:39",
                    "updated_at" => "2021-04-09 14:11:39"
                ],
                [
                    "id" => 12,
                    "parent_id" => 0,
                    "order" => 12,
                    "title" => "????????????",
                    "icon" => "fa-shopping-cart",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-05-05 19:01:13",
                    "updated_at" => "2021-05-05 19:01:13"
                ],
                [
                    "id" => 13,
                    "parent_id" => 12,
                    "order" => 13,
                    "title" => "????????????",
                    "icon" => "fa-shopping-bag",
                    "uri" => "orders",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-05-05 19:03:25",
                    "updated_at" => "2021-05-05 19:03:25"
                ],
                [
                    "id" => 14,
                    "parent_id" => 0,
                    "order" => 14,
                    "title" => "???????????????",
                    "icon" => "fa-credit-card",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-05-19 20:32:49",
                    "updated_at" => "2021-05-19 20:32:49"
                ],
                [
                    "id" => 15,
                    "parent_id" => 14,
                    "order" => 15,
                    "title" => "???????????????",
                    "icon" => "fa-jpy",
                    "uri" => "coupon_codes",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-05-19 20:35:56",
                    "updated_at" => "2021-05-19 20:35:56"
                ],
                [
                    "id" => 16,
                    "parent_id" => 0,
                    "order" => 16,
                    "title" => "????????????",
                    "icon" => "fa-server",
                    "uri" => NULL,
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-05-27 13:35:20",
                    "updated_at" => "2021-05-27 13:44:33"
                ],
                [
                    "id" => 17,
                    "parent_id" => 16,
                    "order" => 17,
                    "title" => "??????????????????",
                    "icon" => "fa-angle-double-right",
                    "uri" => "categories",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-05-27 13:45:27",
                    "updated_at" => "2021-05-27 13:45:27"
                ],
                [
                    "id" => 18,
                    "parent_id" => 10,
                    "order" => 18,
                    "title" => "????????????",
                    "icon" => "fa-cubes",
                    "uri" => "crowdfunding_products",
                    "extension" => "",
                    "show" => 1,
                    "created_at" => "2021-06-15 20:36:36",
                    "updated_at" => "2021-06-15 20:36:36"
                ]
            ]
        );

        Models\Permission::truncate();
        Models\Permission::insert(
            [
                [
                    "id" => 1,
                    "name" => "Auth management",
                    "slug" => "auth-management",
                    "http_method" => "",
                    "http_path" => "",
                    "order" => 1,
                    "parent_id" => 0,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 2,
                    "name" => "Users",
                    "slug" => "users",
                    "http_method" => "",
                    "http_path" => "/auth/users*",
                    "order" => 2,
                    "parent_id" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 3,
                    "name" => "Roles",
                    "slug" => "roles",
                    "http_method" => "",
                    "http_path" => "/auth/roles*",
                    "order" => 3,
                    "parent_id" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 4,
                    "name" => "Permissions",
                    "slug" => "permissions",
                    "http_method" => "",
                    "http_path" => "/auth/permissions*",
                    "order" => 4,
                    "parent_id" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 5,
                    "name" => "Menu",
                    "slug" => "menu",
                    "http_method" => "",
                    "http_path" => "/auth/menu*",
                    "order" => 5,
                    "parent_id" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 6,
                    "name" => "Extension",
                    "slug" => "extension",
                    "http_method" => "",
                    "http_path" => "/auth/extensions*",
                    "order" => 6,
                    "parent_id" => 1,
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => NULL
                ],
                [
                    "id" => 7,
                    "name" => "????????????",
                    "slug" => "????????????",
                    "http_method" => "",
                    "http_path" => "/users*",
                    "order" => 7,
                    "parent_id" => 0,
                    "created_at" => "2021-04-08 22:23:30",
                    "updated_at" => "2021-04-08 22:23:30"
                ],
                [
                    "id" => 8,
                    "name" => "????????????",
                    "slug" => "????????????",
                    "http_method" => "",
                    "http_path" => "/products*",
                    "order" => 8,
                    "parent_id" => 0,
                    "created_at" => "2021-05-22 17:21:41",
                    "updated_at" => "2021-05-22 17:21:41"
                ],
                [
                    "id" => 9,
                    "name" => "????????????",
                    "slug" => "????????????",
                    "http_method" => "",
                    "http_path" => "/orders*",
                    "order" => 9,
                    "parent_id" => 0,
                    "created_at" => "2021-05-22 17:22:14",
                    "updated_at" => "2021-05-22 17:22:14"
                ],
                [
                    "id" => 10,
                    "name" => "???????????????",
                    "slug" => "???????????????",
                    "http_method" => "",
                    "http_path" => "/coupon_codes*",
                    "order" => 10,
                    "parent_id" => 0,
                    "created_at" => "2021-05-22 17:23:01",
                    "updated_at" => "2021-05-22 17:23:01"
                ]
            ]
        );

        Models\Role::truncate();
        Models\Role::insert(
            [
                [
                    "id" => 1,
                    "name" => "Administrator",
                    "slug" => "administrator",
                    "created_at" => "2021-04-08 21:14:53",
                    "updated_at" => "2021-04-08 21:14:53"
                ],
                [
                    "id" => 2,
                    "name" => "??????",
                    "slug" => "operation",
                    "created_at" => "2021-04-08 22:25:14",
                    "updated_at" => "2021-04-08 22:25:14"
                ]
            ]
        );

        Models\Setting::truncate();
		Models\Setting::insert(
			[

            ]
		);

		Models\Extension::truncate();
		Models\Extension::insert(
			[

            ]
		);

		Models\ExtensionHistory::truncate();
		Models\ExtensionHistory::insert(
			[

            ]
		);

        // pivot tables
        DB::table('admin_permission_menu')->truncate();
		DB::table('admin_permission_menu')->insert(
			[
                [
                    "permission_id" => 7,
                    "menu_id" => 8,
                    "created_at" => "2021-05-22 17:29:39",
                    "updated_at" => "2021-05-22 17:29:39"
                ],
                [
                    "permission_id" => 8,
                    "menu_id" => 10,
                    "created_at" => "2021-05-22 17:42:02",
                    "updated_at" => "2021-05-22 17:42:02"
                ],
                [
                    "permission_id" => 9,
                    "menu_id" => 12,
                    "created_at" => "2021-05-22 17:42:22",
                    "updated_at" => "2021-05-22 17:42:22"
                ],
                [
                    "permission_id" => 10,
                    "menu_id" => 14,
                    "created_at" => "2021-05-22 17:42:40",
                    "updated_at" => "2021-05-22 17:42:40"
                ]
            ]
		);

        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 2,
                    "created_at" => "2021-04-08 22:43:38",
                    "updated_at" => "2021-04-08 22:43:38"
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "role_id" => 2,
                    "permission_id" => 7,
                    "created_at" => "2021-04-08 22:25:14",
                    "updated_at" => "2021-04-08 22:25:14"
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 8,
                    "created_at" => "2021-05-22 17:32:16",
                    "updated_at" => "2021-05-22 17:32:16"
                ]
            ]
        );

        // finish
    }
}
