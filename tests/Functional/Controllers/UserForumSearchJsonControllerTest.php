<?php

namespace Tests;

class UserForumSearchJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp(): void
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    protected function getRandomWordsFromSentence($sentence, $count = 5)
    {
        $words = array_filter(
        // splits sentence into words array
            str_word_count($sentence, 1),
            function ($word) {
                // this filters out small words ignored in search
                return strlen($word) > 5;
            }
        );

        shuffle($words);

        return implode(' ', array_slice($words, 0, $count));
    }

    public function test_search_index()
    {
        $author = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $author['id']);

        $posts = [];
        $authors = [$author];

        $postCount = 20;

        for ($i = 0; $i < $postCount; $i++) {

            if ($i % 3 == 0) {
                $author = $this->fakeUser();
                /** @var array $thread */
                $thread = $this->fakeThread($category['id'], $author['id']);
            }

            $posts[] = $this->fakePost($thread['id'], $author['id']);
        }

        $page = 1;
        $limit = 5;
        $topSearchResult = $posts[2];

        // this selects some random words from post content, to assert it later as first result
        $term = $this->getRandomWordsFromSentence($topSearchResult['content']);

        $this->artisan('command:createForumSearchIndexes');

        $response =
            $this->actingAs($this->fakeUser())
                ->call(
                    'GET',
                    self::API_PREFIX . '/search',
                    [
                        'page' => $page,
                        'limit' => $limit,
                        'term' => $term,
                    ]
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $decodedResponse = $response->json();

        // assert results count
        $this->assertLessThanOrEqual($limit, count($decodedResponse['results']));

        // assert top search result
        $this->assertEquals(
            $topSearchResult['id'],
            $decodedResponse['results'][0]['id']
        );

        // assert total results
        $this->assertGreaterThanOrEqual(1, $decodedResponse['total_results']);
    }
}
