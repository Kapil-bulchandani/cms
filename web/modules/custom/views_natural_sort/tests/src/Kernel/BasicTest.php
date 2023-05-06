<?php

namespace Drupal\Tests\views_natural_sort\Kernel;

use Drupal\views_natural_sort\Plugin\IndexRecordContentTransformation\RemoveSymbols;
use Drupal\node\Entity\Node;
use Drupal\Tests\views\Kernel\ViewsKernelTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;

/**
 * Tests for the basic functionality of Views Natural Sort.
 *
 * @group views_natural_sort
 */
class BasicTest extends ViewsKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'comment',
    'node',
    'field',
    'text',
    'user',
    'views_natural_sort',
    'views_natural_sort_test',
  ];

  /**
   * Views to import.
   *
   * @var array
   */
  public static $testViews = ['views_natural_sort_test', 'views_natural_sort_chapter_test'];

  /**
   * {@inheritdoc}
   *
   * @param bool $import_test_views
   *   Should the views specified on the test class be imported. If you need
   *   to setup some additional stuff, like fields, you need to call false and
   *   then call createTestViews for your own.
   */
  protected function setUp($import_test_views = TRUE): void {
    parent::setUp($import_test_views);

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('comment');
    $this->installSchema('views_natural_sort', 'views_natural_sort');
    $this->installConfig([
      'node',
      'user',
      'comment',
      'field',
      'views_natural_sort',
    ]);

    ViewTestData::createTestViews(get_class($this), ['views_natural_sort_test']);
  }

  /**
   * Test the Remove Beginning Words plugin.
   */
  public function testNaturalSortDefaultBeginningWords() {
    $node1 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'A Stripped Zebra',
    ]);
    $node1->save();
    $node2 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'Oklahoma',
    ]);
    $node2->save();
    $node3 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'The King And I',
    ]);
    $node3->save();

    $view = Views::getView('views_natural_sort_test');
    $view->setDisplay();
    $view->preview('default');
    $this->assertIdenticalResultset(
      $view,
      [
        ['title' => 'The King And I'],
        ['title' => 'Oklahoma'],
        ['title' => 'A Stripped Zebra'],
      ],
      ['title' => 'title']
    );
  }

  /**
   * Test the Remove Words plugin.
   */
  public function testNaturalSortDefaultWords() {
    $node1 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'Red of Purple',
    ]);
    $node1->save();
    $node2 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'Red or Green',
    ]);
    $node2->save();
    $node3 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'Red and Blue',
    ]);
    $node3->save();

    $view = Views::getView('views_natural_sort_test');
    $view->setDisplay();
    $view->preview('default');
    $this->assertIdenticalResultset(
      $view,
      [
        ['title' => 'Red and Blue'],
        ['title' => 'Red or Green'],
        ['title' => 'Red of Purple'],
      ],
      ['title' => 'title']
    );
  }

  /**
   * Test the Remove Symbols plugin.
   */
  public function testNaturalSortDefaultSymbols() {
    $node1 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'A(Z',
    ]);
    $node1->save();
    $node2 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'A[B',
    ]);
    $node2->save();
    $node3 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => 'A\\C',
    ]);
    $node3->save();

    $view = Views::getView('views_natural_sort_test');
    $view->setDisplay();
    $view->preview('default');
    $this->assertIdenticalResultset(
      $view,
      [
        ['title' => 'A[B'],
        ['title' => 'A\\C'],
        ['title' => 'A(Z'],
      ],
      ['title' => 'title']
    );
  }

  /**
   * Test Unicode symbol removal in sorting.
   */
  public function testNaturalSortUnicodeSymbols() {
    $plugin = new RemoveSymbols([
      'settings' => "#…\",'\\()[]«?!»¡¿",
    ], '', '');
    $titles = [
      'Cuando… se abre, ¿dará algún tipo de señal?',
    ];
    $expected = [
      'Cuando se abre dará algún tipo de señal',
    ];
    foreach ($titles as $key => $title) {
      $this->assertEquals($plugin->transform($title), $expected[$key]);
    }
  }

  /**
   * Test the Numbers plugin.
   */
  public function testNaturalSortNumbers() {
    $node1 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '1 apple',
    ]);
    $node1->save();
    $node2 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '2 apples',
    ]);
    $node2->save();
    $node3 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '10 apples',
    ]);
    $node3->save();
    $node4 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '-1 apples',
    ]);
    $node4->save();
    $node5 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '-10 apples',
    ]);
    $node5->save();
    $node6 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '-2 apples',
    ]);
    $node6->save();
    $node7 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '-3.550 apples',
    ]);
    $node7->save();
    $node8 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '-3.5501 apples',
    ]);
    $node8->save();
    $node9 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '3.5501 apples',
    ]);
    $node9->save();
    $node0 = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => '3.550 apples',
    ]);
    $node0->save();

    $view = Views::getView('views_natural_sort_test');
    $view->setDisplay();
    $view->preview('default');
    $this->assertIdenticalResultset(
      $view,
      [
        ['title' => '-10 apples'],
        ['title' => '-3.5501 apples'],
        ['title' => '-3.550 apples'],
        ['title' => '-2 apples'],
        ['title' => '-1 apples'],
        ['title' => '1 apple'],
        ['title' => '2 apples'],
        ['title' => '3.550 apples'],
        ['title' => '3.5501 apples'],
        ['title' => '10 apples'],
      ],
      ['title' => 'title']
    );
  }

  /**
   * Test default supported properties.
   */
  public function testSupportedPropertiesCoreTest() {
    $properties = \Drupal::service('views_natural_sort.service')->getViewsSupportedEntityProperties();
    $expected_result = [
      'user' =>
      [
        'name' =>
        [
          'base_table' => 'users_field_data',
          'schema_field' => 'name',
        ],
        'timezone' =>
        [
          'base_table' => 'users_field_data',
          'schema_field' => 'timezone',
        ],
      ],
      'comment' =>
      [
        'subject' =>
        [
          'base_table' => 'comment_field_data',
          'schema_field' => 'subject',
        ],
        'name' =>
        [
          'base_table' => 'comment_field_data',
          'schema_field' => 'name',
        ],
        'hostname' =>
        [
          'base_table' => 'comment_field_data',
          'schema_field' => 'hostname',
        ],
        'entity_type' =>
        [
          'base_table' => 'comment_field_data',
          'schema_field' => 'entity_type',
        ],
        'field_name' =>
        [
          'base_table' => 'comment_field_data',
          'schema_field' => 'field_name',
        ],
      ],
      'node' =>
      [
        'title' =>
        [
          'base_table' => 'node_field_data',
          'schema_field' => 'title',
        ],
      ],
    ];
    $this->assertEquals($properties, $expected_result);
  }

  /**
   * Test storing long unicode characters.
   */
  public function testStoringLongUnicode() {
    $node = Node::create([
      'type' => 'views_natural_sort_test_content',
      'title' => str_repeat('⌘', 255),
    ]);
    $node->save();
    // @todo Drupal Rector Notice: Please delete the following comment after
    // you've made any necessary changes. You will need to use
    // `\Drupal\core\Database\Database::getConnection()` if you do not yet have
    // access to the container here.
    $content = \Drupal::database()->select('views_natural_sort', 'vns')
      ->fields('vns', ['content'])
      ->condition('vns.eid', $node->id())
      ->condition('vns.entity_type', 'node')
      ->execute()
      ->fetchField();
    $this->assertEquals($content, str_repeat('⌘', 255));
  }

  /**
   * Test using custom transformations.
   */
  public function testCustomTransformationPlugin() {
    $node1 = Node::create([
      'type' => 'vns_chapter',
      'title' => '1.1',
    ]);
    $node1->save();
    $node2 = Node::create([
      'type' => 'vns_chapter',
      'title' => '1.10',
    ]);
    $node2->save();
    $node3 = Node::create([
      'type' => 'vns_chapter',
      'title' => '1.2',
    ]);
    $node3->save();

    $view = Views::getView('views_natural_sort_chapter_test');
    $view->setDisplay();
    $view->preview('default');
    $this->assertIdenticalResultset(
      $view,
      [
        ['title' => '1.1'],
        ['title' => '1.2'],
        ['title' => '1.10'],
      ],
      ['title' => 'title']
    );
  }

}
