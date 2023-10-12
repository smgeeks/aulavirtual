<?php

namespace SolidAffiliate\Controllers;

use SolidAffiliate\Lib\Capabilities;
use SolidAffiliate\Lib\SchemaFunctions;
use SolidAffiliate\Lib\ControllerFunctions;
use SolidAffiliate\Lib\Roles;
use SolidAffiliate\Lib\URLs;
use SolidAffiliate\Lib\Validators;
use SolidAffiliate\Lib\VO\Either;
use SolidAffiliate\Lib\VO\Schema;
use SolidAffiliate\Lib\VO\SchemaEntry;
use SolidAffiliate\Models\AffiliateGroup;
use SolidAffiliate\Views\Admin\AffiliateGroups\EditView;
use SolidAffiliate\Views\Admin\AffiliateGroups\NewView;
use SolidAffiliate\Views\Shared\AdminHeader;
use SolidAffiliate\Views\Shared\DeleteResourceView;
use SolidAffiliate\Views\Shared\WPListTableView;


/**
 * AffiliateGroupsController
 * 
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AffiliateGroupsController
{
    const POST_PARAM_SUBMIT_AFFILIATE_GROUP = 'submit_affiliate_group';
    const NONCE_SUBMIT_AFFILIATE = 'solid-affiliate-affiliate-groups-new';

    const POST_PARAM_DELETE = 'delete_affiliate_group';
    const NONCE_DELETE = 'solid-affiliate-affiliate-groups-delete';
    /**
     * This is the function that gets called when somone clicks on the
     * "Manage affiliates" link in the admin side nav. We can use use URL
     * paramaters to decide which page to show (index, show, new, etc.)
     *
     * @return void
     */
    public static function admin_root()
    {
        $action = isset($_GET["action"]) ? (string)$_GET["action"] : "index";
        switch ($action) {
            case "index":
                AffiliateGroupsController::GET_admin_index();
                break;
            case "new":
                AffiliateGroupsController::GET_admin_new();
                break;
            case "edit":
                $ids = ControllerFunctions::extract_ids_from_get($_GET);
                AffiliateGroupsController::GET_admin_edit($ids[0]);
                break;
            case "delete":
                $ids = ControllerFunctions::extract_ids_from_get($_GET);
                AffiliateGroupsController::GET_admin_delete($ids);
                break;
            case "set_default":
                $id = ControllerFunctions::extract_ids_from_get($_GET)[0];
                AffiliateGroup::set_default_group_id($id);
                ControllerFunctions::handle_redirecting_and_exit(URLs::admin_path(AffiliateGroup::ADMIN_PAGE_KEY, true), [], [__('Group set to default.', 'solid-affiliate')], 'admin');
                break;
            default:
                AffiliateGroupsController::GET_admin_index();
                break;
        }
    }

    /**
     * @return void
     */
    public static function GET_admin_index()
    {
        $o = WPListTableView::render(AffiliateGroup::ADMIN_PAGE_KEY, AffiliateGroup::admin_list_table(), __('Affiliate Groups', 'solid-affiliate'));
        echo ($o);
    }

    /**
     * @return void
     */
    public static function GET_admin_new()
    {
        $o = NewView::render();
        echo ($o);
    }

    // Luca: capabilities?
    // Usually you do checks for 'view/edit/delete/update/manage' this kind of content
    // - I might have a user that can CREATE and affiliate but cannot DELETE an affiliate
    //   - the expected UX would be that user gets redirected to a page that says "no you can't do that sir"
    // - User capabilities in multiple places: nonces, views, db level.
    // - expectation that plugin should be able to have developers add/edit/remove custom roles and respective capabilities.
    // - Talk to Louis about multisite
    /**
     * @param int $id
     *
     * @return void
     */
    public static function GET_admin_edit($id)
    {
        $maybe_item = AffiliateGroup::find($id);

        if (is_null($maybe_item)) {
            _e('Not found', 'solid-affiliate');
        } else {
            $item = (object)$maybe_item->attributes;
            echo EditView::render($item);
        }
    }

    /**
     * @param array<int> $ids
     *
     * @return void
     */
    public static function GET_admin_delete($ids)
    {
        $delete_view_configs = [
            'singular_name' => __('Affiliate Group', 'solid-affiliate'),
            'plural_name' => __('Affiliate Groups', 'solid-affiliate'),
            'form_id' => 'affiliate-groups-new',
            'nonce' => AffiliateGroupsController::NONCE_DELETE,
            'submit_action' => AffiliateGroupsController::POST_PARAM_DELETE,
            'children_classes' => []
        ];
        $o = DeleteResourceView::render($delete_view_configs, $ids);
        echo ($o);
    }

    ///////////////////////////////////////////////////////////////////////////
    // POST
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function POST_admin_table_bulk_actions_handler()
    {
        $schema = new Schema(['entries' =>
        [
            'id' => new SchemaEntry([
                'type' => 'varchar',
                'is_enum' => true,
                'display_name' => 'id'
            ]),
            'action' => new SchemaEntry([
                'type' => 'varchar',
                'display_name' => 'action'
            ]),
        ]]);


        $eitherPostParams = ControllerFunctions::extract_and_validate_POST_params(
            $_POST,
            ['id', 'action'],
            $schema
        );

        if ($eitherPostParams->isLeft) {
            ControllerFunctions::handle_redirecting_and_exit('REDIRECT_BACK', $eitherPostParams->left);
        } else {
            $bulk_action_string = (string)$eitherPostParams->right['action'];
            // TODO the idea is that we can encode the data type requirement in a Schema entry,
            // and ControllerFunctions::extract_and_validate_POST_params should take care of ensureing the type.
            // So we wouldn't need to do this below Validator.
            $ids = Validators::array_of_int($eitherPostParams->right['id']);

            switch ($bulk_action_string) {
                case 'Delete':
                    $delete_url = URLs::delete(AffiliateGroup::class, true);
                    ControllerFunctions::handle_redirecting_and_exit($delete_url, [], [], 'admin', ['id' => $ids]);
                    break;
                default:
                    ControllerFunctions::handle_redirecting_and_exit('REDIRECT_BACK', ["Invalid bulk action: {$bulk_action_string}"], [], 'admin');
                    break;
            }

            ControllerFunctions::handle_redirecting_and_exit('REDIRECT_BACK', [], [__('Update Successful', 'solid-affiliate')]);
        };
    }

    /**
     * Creates the Resource
     *
     * @return void
     */
    public static function POST_admin_create_and_update_handler()
    {
        $args   = [
            'page' => AffiliateGroup::ADMIN_PAGE_KEY,
            'class_string' => AffiliateGroup::class,
            'success_msg_create' => __('Successfully created Affiliate Group', 'solid-affiliate'),
            'success_msg_update' => __('Successfully updated Affiliate Group', 'solid-affiliate'),
            'error_msg' => __('There was an error.', 'solid-affiliate'),
            'schema' => AffiliateGroup::schema(),
            'upsert_function' =>
            /** 
             * @param array<string, mixed> $args
             * @return Either<int> */
            function ($args) {
                $eitherId = AffiliateGroup::upsert($args);
                return $eitherId;
            },
            'capability' => Capabilities::EDIT_AFFILIATES
        ];

        ControllerFunctions::generic_upsert_handler($_POST, $args);
    }

    /**
     * @return void
     */
    public static function POST_admin_delete_handler()
    {
        ////////////////////////////////////////////////////////////////////////
        // variables
        $delete_handler_args = [
            'page' => AffiliateGroup::ADMIN_PAGE_KEY,
            'success_msg' => __('Successfully deleted Affiliate Group(s)', 'solid-affiliate'),
            'error_msg' => __('There was an error.', 'solid-affiliate'),
            'delete_by_id_function' =>
            /**
             * @param int $id
             * 
             * @return Either<int>
             */
            function ($id) {
                return AffiliateGroup::delete($id, true);
            },
        ];

        ControllerFunctions::generic_delete_handler($_POST, $delete_handler_args);
        ////////////////////////////////////////////////////////////////////////
    }
}
