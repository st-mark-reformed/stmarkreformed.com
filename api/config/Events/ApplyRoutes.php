<?php

declare(strict_types=1);

namespace Config\Events;

use App\Contact\PostContactAction;
use App\GetKeepAliveAction;
use App\Healthcheck;
use App\HymnsOfTheMonth\Admin\EditHymnOfTheMonthItem\GetEditHymnOfTheMonthItem\GetEditHymnOfTheMonthItemAction;
use App\HymnsOfTheMonth\Admin\EditHymnOfTheMonthItem\PostEditHymnOfTheMonthItem\PostEditHymnOfTheMonthItemAction;
use App\HymnsOfTheMonth\Admin\GetHasEditHymnsOfTheMonthRoleAction;
use App\HymnsOfTheMonth\Admin\GetHymnsOfTheMonthListAction;
use App\HymnsOfTheMonth\Admin\NewHymnOfTheMonthItem\PostNewHymnOfTheMonthItemAction;
use App\HymnsOfTheMonth\Admin\PostDeleteHymnsOfTheMonthItemsAction;
use App\InternalMessages\Admin\EditInternalMessage\GetEditInternalMessage\GetEditInternalMessageAction;
use App\InternalMessages\Admin\EditInternalMessage\PostEditInternalMessage\PostEditInternalMessageAction;
use App\InternalMessages\Admin\GetInternalMessagesListAction;
use App\InternalMessages\Admin\NewInternalMessage\PostNewInternalMessageAction;
use App\InternalMessages\Admin\PostDeleteInternalMessagesAction;
use App\InternalSeries\Admin\EditInternalSeries\GetEditInternalSeries\GetEditInternalSeriesAction;
use App\InternalSeries\Admin\EditInternalSeries\PostEditInternalSeries\PostEditInternalSeriesAction;
use App\InternalSeries\Admin\GetInternalSeriesDropdownAction;
use App\InternalSeries\Admin\GetInternalSeriesListAction;
use App\InternalSeries\Admin\NewInternalSeries\PostNewInternalSeriesAction;
use App\InternalSeries\Admin\PostDeleteInternalSeriesAction;
use App\MailingLists\Admin\EditMailingList\GetEditMailingList\GetEditMailingListAction;
use App\MailingLists\Admin\EditMailingList\PostEditMailingList\PostEditMailingListAction;
use App\MailingLists\Admin\GetHasEditMailingListsRoleAction;
use App\MailingLists\Admin\GetMailingListsListAction;
use App\MailingLists\Admin\NewMailingList\PostNewMailingListAction;
use App\MailingLists\Admin\PostDeleteMailingListsAction;
use App\MenOfTheMark\Admin\EditMenOfTheMarkItem\GetEditMenOfTheMarkItem\GetEditMenOfTheMarkItemAction;
use App\MenOfTheMark\Admin\EditMenOfTheMarkItem\PostEditMenOfTheMarkItem\PostEditMenOfTheMarkItemAction;
use App\MenOfTheMark\Admin\GetHasEditMenOfTheMarkRoleAction;
use App\MenOfTheMark\Admin\GetMenOfTheMarkListAction;
use App\MenOfTheMark\Admin\NewMenOfTheMarkItem\PostNewMenOfTheMarkItemAction;
use App\MenOfTheMark\Admin\PostDeleteMenOfTheMarkItemsAction;
use App\Messages\Admin\EditMessage\GetEditMessage\GetEditMessageAction;
use App\Messages\Admin\EditMessage\PostEditMessage\PostEditMessageAction;
use App\Messages\Admin\GetHasEditMessagesRoleAction;
use App\Messages\Admin\GetMessagesListAction;
use App\Messages\Admin\NewMessage\PostNewMessageAction;
use App\Messages\Admin\PostDeleteMessagesAction;
use App\Messages\Search\GetMessagesSearchAction;
use App\News\Admin\EditNewsItem\GetEditNewsItem\GetEditNewsItemAction;
use App\News\Admin\EditNewsItem\PostEditNewsItem\PostEditNewsItemAction;
use App\News\Admin\GetHasEditNewsRoleAction;
use App\News\Admin\GetNewsListAction;
use App\News\Admin\NewNewsItem\PostNewNewsItemAction;
use App\News\Admin\PostDeleteNewsItemsAction;
use App\PastorsPage\Admin\EditPastorsPageItem\GetEditPastorsPageItem\GetEditPastorsPageItemAction;
use App\PastorsPage\Admin\EditPastorsPageItem\PostEditPastorsPageItem\PostEditPastorsPageItemAction;
use App\PastorsPage\Admin\GetHasEditPastorsPageRoleAction;
use App\PastorsPage\Admin\GetPastorsPageListAction;
use App\PastorsPage\Admin\NewPastorsPageItem\PostNewPastorsPageItemAction;
use App\PastorsPage\Admin\PostDeletePastorsPageItemsAction;
use App\Profiles\Admin\EditProfile\GetEditProfile\GetEditProfileAction;
use App\Profiles\Admin\EditProfile\PostEditProfile\PostEditProfileAction;
use App\Profiles\Admin\GetHasEditProfilesRoleAction;
use App\Profiles\Admin\GetLeadershipPositionsAction;
use App\Profiles\Admin\GetProfilesDropdownValues;
use App\Profiles\Admin\GetProfilesListAction;
use App\Profiles\Admin\NewProfile\PostNewProfileAction;
use App\Profiles\Admin\PostDeleteProfilesAction;
use App\Queue\GetAdminQueueAction;
use App\Queue\GetAdminQueueFailedAction;
use App\Queue\GetAdminQueueStatusAction;
use App\Queue\PostRetryFailedQueueItemAction;
use App\Resources\Admin\EditResourceItem\GetEditResourceItem\GetEditResourceItemAction;
use App\Resources\Admin\EditResourceItem\PostEditResourceItem\PostEditResourceItemAction;
use App\Resources\Admin\GetHasEditResourcesRoleAction;
use App\Resources\Admin\GetResourcesListAction;
use App\Resources\Admin\NewResourceItem\PostNewResourceItemAction;
use App\Resources\Admin\PostDeleteResourceItemsAction;
use App\Schedule\Admin\GetAdminScheduleAction;
use App\Series\Admin\EditSeries\GetEditSeries\GetEditSeriesAction;
use App\Series\Admin\EditSeries\PostEditSeries\PostEditSeriesAction;
use App\Series\Admin\GetSeriesDropdownAction;
use App\Series\Admin\GetSeriesListAction;
use App\Series\Admin\NewSeries\PostNewSeriesAction;
use App\Series\Admin\PostDeleteSeriesAction;
use App\Tinker;
use BuzzingPixel\Queue\Http\Routes\Route;
use BuzzingPixel\Queue\Http\Routes\RoutesFactory as QueueRoutesFactory;
use Config\RuntimeConfigOptions;
use RxAnte\AppBootstrap\Http\ApplyRoutesEvent;
use RxAnte\AppBootstrap\RuntimeConfig;

use function assert;

readonly class ApplyRoutes
{
    public function onDispatch(ApplyRoutesEvent $routes): void
    {
        $config = $routes->getContainer()->get(RuntimeConfig::class);
        assert($config instanceof RuntimeConfig);

        if ($config->getBoolean(RuntimeConfigOptions::DEV_MODE)) {
            Tinker::applyRoute(routes: $routes);
        }

        Healthcheck::applyRoute(routes: $routes);
        PostContactAction::applyRoute(routes: $routes);
        GetProfilesListAction::applyRoute(routes: $routes);
        GetLeadershipPositionsAction::applyRoute(routes: $routes);
        GetHasEditProfilesRoleAction::applyRoute(routes: $routes);
        PostNewProfileAction::applyRoute(routes: $routes);
        GetKeepAliveAction::applyRoute(routes: $routes);
        GetEditProfileAction::applyRoute(routes: $routes);
        PostEditProfileAction::applyRoute(routes: $routes);
        GetHasEditMessagesRoleAction::applyRoute(routes: $routes);
        PostNewSeriesAction::applyRoute(routes: $routes);
        GetSeriesListAction::applyRoute(routes: $routes);
        GetEditSeriesAction::applyRoute(routes: $routes);
        PostEditSeriesAction::applyRoute(routes: $routes);
        PostNewMessageAction::applyRoute(routes: $routes);
        GetMessagesListAction::applyRoute(routes: $routes);
        GetEditMessageAction::applyRoute(routes: $routes);
        PostEditMessageAction::applyRoute(routes: $routes);
        GetProfilesDropdownValues::applyRoute(routes: $routes);
        GetSeriesDropdownAction::applyRoute(routes: $routes);
        PostDeleteMessagesAction::applyRoute(routes: $routes);
        PostDeleteSeriesAction::applyRoute(routes: $routes);
        PostDeleteProfilesAction::applyRoute(routes: $routes);
        GetAdminScheduleAction::applyRoute(routes: $routes);
        GetAdminQueueAction::applyRoute(routes: $routes);
        GetAdminQueueFailedAction::applyRoute(routes: $routes);
        PostRetryFailedQueueItemAction::applyRoute(routes: $routes);
        GetAdminQueueStatusAction::applyRoute(routes: $routes);
        GetMessagesSearchAction::applyRoute(routes: $routes);

        // Internal Messages
        PostNewInternalMessageAction::applyRoute(routes: $routes);
        GetInternalMessagesListAction::applyRoute(routes: $routes);
        GetEditInternalMessageAction::applyRoute(routes: $routes);
        PostEditInternalMessageAction::applyRoute(routes: $routes);
        PostDeleteInternalMessagesAction::applyRoute(routes: $routes);

        // Internal Series
        PostNewInternalSeriesAction::applyRoute(routes: $routes);
        GetInternalSeriesListAction::applyRoute(routes: $routes);
        GetEditInternalSeriesAction::applyRoute(routes: $routes);
        PostEditInternalSeriesAction::applyRoute(routes: $routes);
        GetInternalSeriesDropdownAction::applyRoute(routes: $routes);
        PostDeleteInternalSeriesAction::applyRoute(routes: $routes);

        // News
        GetHasEditNewsRoleAction::applyRoute(routes: $routes);
        PostNewNewsItemAction::applyRoute(routes: $routes);
        GetNewsListAction::applyRoute(routes: $routes);
        GetEditNewsItemAction::applyRoute(routes: $routes);
        PostEditNewsItemAction::applyRoute(routes: $routes);
        PostDeleteNewsItemsAction::applyRoute(routes: $routes);

        // Men of the Mark
        GetHasEditMenOfTheMarkRoleAction::applyRoute(routes: $routes);
        PostNewMenOfTheMarkItemAction::applyRoute(routes: $routes);
        GetMenOfTheMarkListAction::applyRoute(routes: $routes);
        GetEditMenOfTheMarkItemAction::applyRoute(routes: $routes);
        PostEditMenOfTheMarkItemAction::applyRoute(routes: $routes);
        PostDeleteMenOfTheMarkItemsAction::applyRoute(routes: $routes);

        // Pastors Page
        GetHasEditPastorsPageRoleAction::applyRoute(routes: $routes);
        PostNewPastorsPageItemAction::applyRoute(routes: $routes);
        GetPastorsPageListAction::applyRoute(routes: $routes);
        GetEditPastorsPageItemAction::applyRoute(routes: $routes);
        PostEditPastorsPageItemAction::applyRoute(routes: $routes);
        PostDeletePastorsPageItemsAction::applyRoute(routes: $routes);

        // Hymns of the Month
        GetHasEditHymnsOfTheMonthRoleAction::applyRoute(routes: $routes);
        PostNewHymnOfTheMonthItemAction::applyRoute(routes: $routes);
        GetHymnsOfTheMonthListAction::applyRoute(routes: $routes);
        GetEditHymnOfTheMonthItemAction::applyRoute(routes: $routes);
        PostEditHymnOfTheMonthItemAction::applyRoute(routes: $routes);
        PostDeleteHymnsOfTheMonthItemsAction::applyRoute(routes: $routes);

        // Resources
        GetHasEditResourcesRoleAction::applyRoute(routes: $routes);
        PostNewResourceItemAction::applyRoute(routes: $routes);
        GetResourcesListAction::applyRoute(routes: $routes);
        GetEditResourceItemAction::applyRoute(routes: $routes);
        PostEditResourceItemAction::applyRoute(routes: $routes);
        PostDeleteResourceItemsAction::applyRoute(routes: $routes);

        // Mailing Lists
        GetHasEditMailingListsRoleAction::applyRoute(routes: $routes);
        PostNewMailingListAction::applyRoute(routes: $routes);
        GetMailingListsListAction::applyRoute(routes: $routes);
        GetEditMailingListAction::applyRoute(routes: $routes);
        PostEditMailingListAction::applyRoute(routes: $routes);
        PostDeleteMailingListsAction::applyRoute(routes: $routes);

        if (
            ! $config->getBoolean(
                RuntimeConfigOptions::ENABLE_QUEUE_MANAGEMENT_ROUTES,
            )
        ) {
            return;
        }

        $this->createQueueInterfaceRoutes($routes);
    }

    private function createQueueInterfaceRoutes(ApplyRoutesEvent $routes): void
    {
        $queueRoutesFactory = $routes->getContainer()->get(
            QueueRoutesFactory::class,
        );

        assert($queueRoutesFactory instanceof QueueRoutesFactory);

        $queueRoutes = $queueRoutesFactory->create();

        $queueRoutes->map(
            static function (Route $route) use ($routes): void {
                $routes->map(
                    [$route->requestMethod->name],
                    $route->pattern,
                    $route->class,
                );
            },
        );
    }
}
