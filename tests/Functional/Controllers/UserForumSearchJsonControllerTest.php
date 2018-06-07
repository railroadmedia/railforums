<?php

namespace Tests;

class UserForumSearchJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    protected function getRandomWordsFromSentence($sentence, $count = 5)
    {
        $words = array_filter(
            // splits sentence into words array
            str_word_count($sentence, 1),
            function($word) {
                // this filters out small words ignored in search
                return strlen($word) > 5;
            }
        );

        shuffle($words);

        return implode(' ', array_slice($words, 0, $count));
    }

    public function test_search_index()
    {
        $user = $this->fakeCurrentUserCloak();
        $author = $this->fakeUserCloak();
        $category = $this->fakeCategory();
        $thread = $this->fakeThread($category->getId(), $author->getId());
        $posts = [];
        $threads = [$thread];
        $authors = [$author];

        $postCount = 20;

        for ($i = 0; $i < $postCount; $i++) {

            if ($i % 3 == 0) {
                $author = $this->fakeUserCloak();
                $authors[] = $author;
                $thread = $this->fakeThread($category->getId(), $author->getId());
                $threads[] = $thread;
            }

            $posts[] = $this->fakePost($thread->getId(), $author->getId());
        }

        $page = 1;
        $limit = 5;
        $type = ''; // posts | threads
        $topSearchResult = $threads[2];

        // this selects some random words from thread title, to assert it later as first result
        $term = $this->getRandomWordsFromSentence($topSearchResult->getTitle());

        // $sidm = $this->app->make(\Railroad\Railforums\DataMappers\SearchIndexDataMapper::class);
        // $sidm->search($term, '', $page, $limit, 'score');

        $command = $this->app->make(\Railroad\Railforums\Commands\CreateSearchIndexes::class);
        $command->handle();

        // $this->artisan('command:createSearchIndexes'); // TODO - make it work and remove the above

        $response = $this->call('GET', self::API_PREFIX . '/search', [
            'page' => $page,
            'limit' => $limit,
            'term' => $term,
            'type' => $type
        ]);

        // TODO - assert response
        $this->assertTrue(true);
    }
}
