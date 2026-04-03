<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permission create 
        $permissions = [
            /**
             * role manage for system
             * if system give permission to admin
             */
            'role_navigation', // show role manage navigation to user panel
            'role_list',
            'role_create',
            'role_edit',
            'role_delete',
            'role_manage',

            /**
             * permission manage for system
             * if system give permission to admin
             */
            // 'permission_navigation',
            // 'permission_list',
            // 'permission_create',
            // 'permission_edit',
            // 'permission_delete',
            // 'permission_manage',

            /**
             * permission fixed for system
             * those permission define the ability to add and remove role and permission
             * to user
             */
            'sync_role_to_user',
            'sync_permission_to_role',
            'sync_permision_to_user',
            'sync_settings',

            /**
             * admin must have hold the access point permission 
             * to access the system dashboard section. 
             */

            // 'access_admin',
            // 'access_vendor',
            // 'access_reseller',
            // 'access_users',
            // 'access_rider',
            // 'access_comision',
            // 'access_withdraw',
            // 'access_role',
            // 'access_permission',
            // 'access_permission',
            // 'access_vip',
            // 'access_store_section',
            // 'access_product_section',
            // 'access_category_section',
            // 'access_order_section',
            // 'access_slider_section',
            // 'access_deposit_section',


            'access_ticket_section',
            'access_setting_section',
            'access_shipping_section',

            /**
             * role holder hold access point permission 
             * to access the dashboard 
             */
            'access_users_dashboard', // by permission, user can access user panel
            'access_vendor_dashboard', // display 'Go To Vendor' section to user panel, if user if vendor 
            'access_reseller_dashboard', // display 'Go To Reseller' section to user panel, if user is reseller
            'access_rider_dashboard', // display 'Go To Rider' section, if user is rider


            /**
             * Permission belongs to system role
             * to operate adminship
             * if system gives permission to other
             */
            'admin_navigation', // show admin manage navigation to user panel
            'admin_manage',
            'admin_view',
            'admin_add',
            'admin_edit',
            'admin_update',
            'admin_delete',

            'admin_can_manage_comissions',
            'admin_can_manage_withdraws',
            'admin_can_manage_orders',


            /**
             * permission for admin task
             */
            'vendors_navigation', // show Manage Vendor navigation to the user panel
            'vendors_manage',
            'vendors_view',
            'vendors_add',
            'vendors_edit',
            'vendors_delelte',
            'vendors_update',


            /**
             * vendor permissions
             */
            'shop_sell_product',
            'shop_manage_order',
            'shop_view_shops',

            /**
             * reseller
             */
            'resellers_view_vendors_products',
            'resellers_view_vendors_categories',

            /**
             * permission for admin task
             */
            'resellers_navigation', // show reseller Manage navigation to the user panel 
            'resellers_view',
            'resellers_add',
            'resellers_edit',
            'resellers_delete',
            'resellers_update',
            'resellers_manage',

            /**
             * permission for admin task
             */
            'riders_navigation', // show rider manage navigation to the user panel
            'riders_view',
            'riders_add',
            'riders_edit',
            'riders_update',
            'riders_delete',
            'riders_manage',

            /**
             * permision for admin task
             */
            'users_navigation', // show User Manage navigation to the user panel
            'users_view',
            'users_manage',
            'users_add',
            'users_edit',
            'users_delete',
            'users_update',


            /**
             * category permission for vendor and admin
             */
            'category_navigation',
            'category_view',
            'category_add',
            'category_edit',
            'category_update',
            'category_delete',

            /**
             * product permission for admin and vendor
             */
            'product_navigation',
            'product_view',
            'product_add',
            'product_edit',
            'product_update',
            'product_delete',

            // comissions permission
            'comission_navigation',
            'comission_view',
            'comission_confim',
            'comission_update',
            'comission_delete',


            // withdraw
            'withdraw_navigation',
            'withdraw_view',
            'withdraw_confirm',
            'withdraw_reject',


            // vip
            'vip_navigation',
            'vip_view',
            'vip_add',
            'vip_delete',
            'vip_update',

            // vip user
            'vip_user_view',
            'vip_user_confirm',
            'vip_user_edit',
            'vip_user_migrate',
            'vip_user_reject',
            'vip_user_delete',


            // store 
            'store_navigation',
            'store_view',
            'store_add',


            // order
            'order_navigation',
            'order_view',
            'order_manage',
            'order_confirm',
            'order_delete',
            // 'order_accept',
            // 'order_picked',
            // 'order_delivery',
            // 'order_delivered',
            // 'order_finished',
            // 'order_cancel',
            // 'order_hold',
            // 'order_pending',


            // slider
            'slider_navigation',
            'slider_view',
            'slider_add',
            'slider_edit',
            'slider_delete',


            // deposit
            'deposit_navigation',
            'deposit_view',
            'deposit_confirm',
            'deposit_reject',


            // partnership
            'partnership_developer',
            'partnership_management',


        ];

        foreach ($permissions as $value) {
            Permission::create(['name' => $value]);
        }
    }
}
