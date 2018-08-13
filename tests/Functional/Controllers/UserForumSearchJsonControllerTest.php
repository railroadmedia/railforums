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
        $this->fakeCurrentUserCloak();

        $author = $this->fakeUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $author->getId());

        $posts = [];
        $authors = [$author];

        $postCount = 20;

        for ($i = 0; $i < $postCount; $i++) {

            if ($i % 3 == 0) {
                $author = $this->fakeUserCloak();
                /** @var array $thread */
                $thread = $this->fakeThread($category['id'], $author->getId());
            }

            $posts[] = $this->fakePost($thread['id'], $author->getId());
        }

        $page = 1;
        $limit = 5;
        $topSearchResult = $posts[2];

        // this selects some random words from post content, to assert it later as first result
        $term = $this->getRandomWordsFromSentence($topSearchResult['content']);

        $this->artisan('command:createSearchIndexes');

        $response = $this->call('GET', self::API_PREFIX . '/search', [
            'page' => $page,
            'limit' => $limit,
            'term' => $term
        ]);

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $decodedResponse = $response->decodeResponseJson();

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
