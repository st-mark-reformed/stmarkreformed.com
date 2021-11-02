<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use craft\elements\MatrixBlock;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function count;

class Leadership implements BlockResponseBuilderContract
{
    private const POSITIONS = [
        'pastor',
        'rulingElder',
        'elder',
        'deacon',
    ];

    private const POSITION_TITLES = [
        'pastor' => 'Pastor',
        'rulingElder' => 'Ruling Elder',
        'elder' => 'Elder',
        'deacon' => 'Deacon',
    ];

    private const POSITION_TITLES_PLURAL = [
        'pastor' => 'Pastors',
        'rulingElder' => 'Ruling Elders',
        'elder' => 'Elders',
        'deacon' => 'Deacons',
    ];

    public function __construct(
        private TwigEnvironment $twig,
        private RetrievePeople $retrievePeople,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        $leadershipSections = [];

        foreach (self::POSITIONS as $position) {
            $models = $this->retrievePeople->retrieve(position:  $position);

            $total = count($models);

            if ($total < 1) {
                continue;
            }

            $leadershipSections[] = new LeadershipSectionContentModel(
                sectionTitle: $total > 1 ?
                    self::POSITION_TITLES_PLURAL[$position] :
                    self::POSITION_TITLES[$position],
                people: $models,
            );
        }

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/Leadership/Leadership.twig',
            [
                'contentModel' => new LeadershipContentModel(
                    leadershipSections: $leadershipSections,
                ),
            ],
        );
    }
}
