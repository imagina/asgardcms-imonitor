<?php

namespace Modules\Imonitor\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterImonitorSidebar implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(BuildingSidebar $sidebar)
    {
        $sidebar->add($this->extendWith($sidebar->getMenu()));
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('core::sidebar.content'), function (Group $group) {
            $group->item(trans('imonitor::imonitors.title.imonitors'), function (Item $item) {
                $item->icon('fa fa-copy');
                $item->weight(10);
                $item->authorize(
                     /* append */
                );
                $item->item(trans('imonitor::products.title.products'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.imonitor.product.create');
                    $item->route('admin.imonitor.product.index');
                    $item->authorize(
                        $this->auth->hasAccess('imonitor.products.index')
                    );
                });
                $item->item(trans('imonitor::variables.title.variables'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.imonitor.variable.create');
                    $item->route('admin.imonitor.variable.index');
                    $item->authorize(
                        $this->auth->hasAccess('imonitor.variables.index')
                    );
                });
                $item->item(trans('imonitor::records.title.records'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.imonitor.record.create');
                    $item->route('admin.imonitor.record.index');
                    $item->authorize(
                        $this->auth->hasAccess('imonitor.records.index')
                    );
                });
// append



            });
        });

        return $menu;
    }
}
