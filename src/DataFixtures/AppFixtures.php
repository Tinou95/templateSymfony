<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Movie;
use App\Entity\Playlist;
use App\Entity\PlaylistMedia;
use App\Entity\PlaylistSubscription;
use App\Entity\Season;
use App\Entity\Serie;
use App\Entity\Subscription;
use App\Entity\SubscriptionHistory;
use App\Entity\User;
use App\Enum\CommentStatusEnum;
use App\Enum\UserAccountStatusEnum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const MAX_USERS = 10;
    public const MAX_MEDIA = 100;
    public const MAX_SUBSCRIPTIONS = 3;
    public const MAX_SEASONS = 3;
    public const MAX_EPISODES = 10;

    public const PLAYLISTS_PER_USER = 3;
    public const MAX_MEDIA_PER_PLAYLIST = 3;
    public const MAX_LANGUAGE_PER_MEDIA = 3;
    public const MAX_CATEGORY_PER_MEDIA = 3;
    public const MAX_SUBSCRIPTIONS_HISTORY_PER_USER = 3;
    public const MAX_COMMENTS_PER_MEDIA = 10;
    public const MAX_PLAYLIST_SUBSCRIPTION_PER_USERS = 3;

    public function load(ObjectManager $manager): void
    {
        $users = [];
        $medias = [];
        $playlists = [];
        $categories = [];
        $languages = [];
        $subscriptions = [];

        $this->createUsers($manager, $users);
        $this->createPlaylists($manager, $users, $playlists);
        $this->createSubscriptions($manager, $users, $subscriptions);
        $this->createCategories($manager, $categories);
        $this->createLanguages($manager, $languages);
        $this->createMedia($manager, $medias);
        $this->createComments($manager, $medias, $users);

        $this->linkMediaToPlaylists($medias, $playlists, $manager);
        $this->linkSubscriptionToUsers($users, $subscriptions, $manager);
        $this->linkMediaToCategories($medias, $categories);
        $this->linkMediaToLanguages($medias, $languages);

        $this->addUserPlaylistSubscriptions($manager, $users, $playlists);

        $manager->flush();
    }

    protected function createSubscriptions(ObjectManager $manager, array $users, array &$subscriptions): void
    {
        $array = [
            ['name' => 'Basic 1 Month - HD', 'duration' => 1, 'price' => 4],
            ['name' => 'Basic 3 Months - HD', 'duration' => 3, 'price' => 10],
            ['name' => 'Premium 6 Months - HD', 'duration' => 6, 'price' => 18],
            ['name' => 'Ultimate 1 Year - 4K', 'duration' => 12, 'price' => 40],
            ['name' => 'Standard 1 Month - 4K HDR', 'duration' => 1, 'price' => 7],
            ['name' => 'Standard 3 Months - 4K HDR', 'duration' => 3, 'price' => 17],
            ['name' => 'Advanced 6 Months - 4K HDR', 'duration' => 6, 'price' => 35],
            ['name' => 'Pro 1 Year - 8K', 'duration' => 12, 'price' => 60],
        ];

        foreach ($array as $element) {
            $subscription = new Subscription();
            $subscription->setDuration($element['duration']);
            $subscription->setName($element['name']);
            $subscription->setPrice($element['price']);
            $manager->persist($subscription);
            $subscriptions[] = $subscription;

            for ($i = 0; $i < random_int(1, self::MAX_SUBSCRIPTIONS); ++$i) {
                $randomUser = $users[array_rand($users)];
                $randomUser->setCurrentSubscription($subscription);
            }
        }
    }

    protected function createMedia(ObjectManager $manager, array &$medias): void
    {
        for ($j = 0; $j < self::MAX_MEDIA; ++$j) {
            $media = 0 === random_int(min: 0, max: 1) ? new Movie() : new Serie();
            $title = $media instanceof Movie ? 'Film' : 'Série';

            $media->setTitle("Film ou Série n°$j");
            $media->setLongDescription("Longue description unique $j");
            $media->setShortDescription("Description courte $j");
            $media->setCoverImage("https://picsum.photos/1920/1080?random=$j");
            $media->setReleaseDate(new \DateTime('+7 days'));
            $manager->persist($media);
            $medias[] = $media;

            $this->addCastingAndStaff($media);

            if ($media instanceof Serie) {
                $this->createSeasons($manager, $media);
            }
        }
    }

    protected function createUsers(ObjectManager $manager, array &$users): void
    {
        for ($i = 0; $i < self::MAX_USERS; ++$i) {
            $user = new User();
            $user->setEmail("user_$i@example.com");
            $user->setUsername("user_$i");
            $user->setPassword('password123');
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
            $users[] = $user;

            $manager->persist($user);
        }
    }

    public function createPlaylists(ObjectManager $manager, array $users, array &$playlists): void
    {
        foreach ($users as $user) {
            for ($k = 0; $k < self::PLAYLISTS_PER_USER; ++$k) {
                $playlist = new Playlist();
                $playlist->setName("Playlist numéro $k");
                $playlist->setCreatedAt(new \DateTimeImmutable());
                $playlist->setUpdatedAt(new \DateTimeImmutable());
                $playlist->setCreator($user);
                $playlists[] = $playlist;

                $manager->persist($playlist);
            }
        }
    }

    protected function createCategories(ObjectManager $manager, array &$categories): void
    {
        $array = [
            ['nom' => 'Aventure', 'label' => 'Aventure'],
            ['nom' => 'Comédie musicale', 'label' => 'Comédie musicale'],
            ['nom' => 'Biographie', 'label' => 'Biographie'],
            ['nom' => 'Horreur psychologique', 'label' => 'Horreur psychologique'],
            ['nom' => 'Fantastique', 'label' => 'Fantastique'],
            ['nom' => 'Suspense', 'label' => 'Suspense'],
        ];

        foreach ($array as $element) {
            $category = new Category();
            $category->setNom($element['nom']);
            $category->setLabel($element['label']);
            $manager->persist($category);
            $categories[] = $category;
        }
    }

    protected function createLanguages(ObjectManager $manager, array &$languages): void
    {
        $array = [
            ['code' => 'fr', 'nom' => 'Français'],
            ['code' => 'en', 'nom' => 'Anglais'],
            ['code' => 'it', 'nom' => 'Italien'],
            ['code' => 'de', 'nom' => 'Allemand'],
            ['code' => 'ja', 'nom' => 'Japonais'],
        ];

        foreach ($array as $element) {
            $language = new Language();
            $language->setCode($element['code']);
            $language->setNom($element['nom']);
            $manager->persist($language);
            $languages[] = $language;
        }
    }

    protected function createSeasons(ObjectManager $manager, Serie $media): void
    {
        for ($i = 0; $i < random_int(1, self::MAX_SEASONS); ++$i) {
            $season = new Season();
            $season->setNumber('Saison '.($i + 1));
            $season->setSerie($media);

            $manager->persist($season);
            $this->createEpisodes($season, $manager);
        }
    }

    protected function createEpisodes(Season $season, ObjectManager $manager): void
    {
        for ($i = 0; $i < random_int(1, self::MAX_EPISODES); ++$i) {
            $episode = new Episode();
            $episode->setTitle("Épisode ".($i + 1));
            $episode->setDuration(random_int(20, 80));
            $episode->setReleasedAt(new \DateTimeImmutable());
            $episode->setSeason($season);

            $manager->persist($episode);
        }
    }

    protected function createComments(ObjectManager $manager, array $medias, array $users): void
    {
        foreach ($medias as $media) {
            for ($i = 0; $i < random_int(1, self::MAX_COMMENTS_PER_MEDIA); ++$i) {
                $comment = new Comment();
                $comment->setPublisher($users[array_rand($users)]);
                $comment->setMessage("Message commenté $i");
                $comment->setStatus(CommentStatusEnum::PUBLISHED);
                $comment->setMedia($media);

                $manager->persist($comment);
            }
        }
    }

    protected function linkMediaToCategories(array $medias, array $categories): void
    {
        foreach ($medias as $media) {
            $category = $categories[array_rand($categories)];
            $media->addCategory($category);
        }
    }

    protected function linkMediaToLanguages(array $medias, array $languages): void
    {
        foreach ($medias as $media) {
            for ($i = 0; $i < random_int(1, self::MAX_LANGUAGE_PER_MEDIA); ++$i) {
                $language = $languages[array_rand($languages)];
                $media->addLanguage($language);
            }
        }
    }

    protected function linkMediaToPlaylists(array $medias, array $playlists, ObjectManager $manager): void
    {
        foreach ($medias as $media) {
            for ($i = 0; $i < random_int(1, self::MAX_PLAYLISTS_PER_USER); ++$i) {
                $playlist = $playlists[array_rand($playlists)];
                $playlistMedia = new PlaylistMedia();
                $playlistMedia->setPlaylist($playlist);
                $playlistMedia->setMedia($media);
                $manager->persist($playlistMedia);
            }
        }
    }

    protected function linkSubscriptionToUsers(array $users, array $subscriptions, ObjectManager $manager): void
    {
        foreach ($users as $user) {
            $subscription = $subscriptions[array_rand($subscriptions)];
            $subscriptionHistory = new SubscriptionHistory();
            $subscriptionHistory->setUser($user);
            $subscriptionHistory->setSubscription($subscription);
            $subscriptionHistory->setStartDate(new DateTimeImmutable());
            $manager->persist($subscriptionHistory);
        }
    }

    protected function addUserPlaylistSubscriptions(ObjectManager $manager, array $users, array $playlists): void
    {
        foreach ($users as $user) {
            for ($i = 0; $i < random_int(1, self::MAX_PLAYLIST_SUBSCRIPTION_PER_USERS); ++$i) {
                $playlist = $playlists[array_rand($playlists)];
                $playlistSubscription = new PlaylistSubscription();
                $playlistSubscription->setUser($user);
                $playlistSubscription->setPlaylist($playlist);
                $manager->persist($playlistSubscription);
            }
        }
    }
}
