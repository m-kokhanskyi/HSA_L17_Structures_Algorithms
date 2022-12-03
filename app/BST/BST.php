<?php

class Node
{
    public int   $value;
    public ?Node $left   = null;
    public ?Node $right  = null;
    public ?Node $parent = null;

    public function __construct(int $value, Node $parent = null)
    {
        $this->value  = $value;
        $this->parent = $parent;
    }

    public function delete(): self
    {
        if ($this->left && $this->right) {
            $min         = $this->right->min();
            $this->value = $min->value;
            $min->delete();
        } elseif ($this->right) {
            if ($this->parent?->left === $this) {
                $this->parent->left  = $this->right;
                $this->right->parent = $this->parent->left;
            } elseif ($this->parent?->right === $this) {
                $this->parent->right = $this->right;
                $this->right->parent = $this->parent->right;
            }
            $this->parent = null;
            $this->right  = null;
        } elseif ($this->left) {
            if ($this->parent?->left === $this) {
                $this->parent->left = $this->left;
                $this->left->parent = $this->parent->left;
            } elseif ($this->parent?->right === $this) {
                $this->parent->right = $this->left;
                $this->left->parent  = $this->parent->right;
            }
            $this->parent = null;
            $this->left   = null;
        } else {
            if ($this->parent?->right === $this) {
                $this->parent->right = null;
            } elseif ($this->parent?->left === $this) {
                $this->parent->left = null;
            }
            $this->parent = null;
        }

        return $this;
    }

    public function max(): Node
    {
        $node = $this;
        while ($node->right) {
            if (!$node->right) {
                break;
            }
            $node = $node->right;
        }

        return $node;
    }

    public function min(): Node
    {
        $node = $this;
        while ($node->left) {
            if (!$node->left) {
                break;
            }
            $node = $node->left;
        }

        return $node;
    }
}

class BST
{
    public ?Node $root = null;

    public function __construct(int $value = null)
    {
        if ($value !== null) {
            $this->root = new Node($value);
        }
    }

    public function balance(array $list): self
    {
        sort($list);
        $chunks = array_chunk($list, (int)ceil(count($list) / 2));
        $mid    = array_pop($chunks[0]);
        $this->insert($mid);
        if (!empty($chunks[0])) {
            $this->balance($chunks[0]);
        }
        if (!empty($chunks[1])) {
            $this->balance($chunks[1]);
        }

        return $this;
    }

    public function delete($value): void
    {
        $node = $this->search($value);
        if ($node) {
            $node->delete();
        }
    }

    public function insert(int $value): ?Node
    {
        $node = $this->root;
        if (!$node) {
            return $this->root = new Node($value);
        }

        while ($node !== null) {
            if ($value > $node->value) {
                if ($node->right) {
                    $node = $node->right;
                } else {
                    $node = $node->right = new Node($value, $node);
                    break;
                }
            } else {
                if ($value < $node->value) {
                    if ($node->left) {
                        $node = $node->left;
                    } else {
                        $node = $node->left = new Node($value, $node);
                        break;
                    }
                } else {
                    break;
                }
            }
        }

        return $node;
    }

    public function max(): Node
    {
        if (!$this->root) {
            throw new Exception('Tree root is empty!');
        }

        $node = $this->root;

        return $node->max();
    }

    public function min(): Node
    {
        if ($this->root === null) {
            throw new Exception('Tree root is empty!');
        }

        $node = $this->root;

        return $node->min();
    }

    public function search(int $value): ?Node
    {
        $node = $this->root;

        while ($node) {
            if ($value > $node->value) {
                $node = $node->right;
            } elseif ($value < $node->value) {
                $node = $node->left;
            } else {
                break;
            }
        }

        return $node;
    }
}
$json = [];

echo 'insert' . PHP_EOL;
$insertSet = [];
for ($i = 1; $i <= 100; $i++) {
    $bst = new BST();
    $multiplier = 10;
    $itemsCount = 10 * $i * $multiplier;
    $rangeFrom  = $i * $itemsCount;
    $rangeTo    = 1000 * $i * $itemsCount;
    $dataset    = [];
    for ($j = 0; $j < $itemsCount; $j++) {
        $dataset[] = random_int($rangeFrom, $rangeTo);
    }
    $bst->balance($dataset);
    $start = microtime(true);
    $bst->insert((int)ceil(max($dataset)));
}
//
//echo PHP_EOL . '____________________' . PHP_EOL;
//echo PHP_EOL . 'search' . PHP_EOL;
//for ($i = 1; $i <= 100; $i++) {
//    $bst = new BST();
//    $multiplier = 10;
//    $itemsCount = 10 * $i * $multiplier;
//    $rangeFrom  = $i * $itemsCount;
//    $rangeTo    = 1000 * $i * $itemsCount;
//    $dataset    = [];
//    $value = $rangeTo;
//    for ($j = 0; $j < $itemsCount; $j++) {
//        $dataset[] = random_int($rangeFrom, $rangeTo);
//    }
//    $bst->balance($dataset);
//    $start = microtime(true);
//    $bst->search((int)ceil(max($dataset)));
//    $resultSec = microtime(true) - $start;
//    echo $itemsCount . '-' . $resultSec . PHP_EOL;
//    $json[$itemsCount] = array_merge($json[$itemsCount], ['search' => $resultSec]);
//}

echo PHP_EOL . '____________________' . PHP_EOL;
echo PHP_EOL . 'delete' . PHP_EOL;
$insertSet = [];
for ($i = 1; $i <= 100; $i++) {
    $multiplier = 10;
    $itemsCount = 10 * $i * $multiplier;
    $rangeFrom  = $i * $itemsCount;
    $rangeTo    = 1000 * $i * $itemsCount;
    $dataset    = [];
    $value = $rangeTo;
    for ($j = 0; $j < $itemsCount; $j++) {
        $value = random_int($rangeFrom, $rangeTo);
        $dataset[] = $value;
    }
    $bst->balance($dataset);
    $start = microtime(true);
    $bst->delete((int)ceil(max($dataset)));
    $resultSec = microtime(true) - $start;
    echo $itemsCount . '-' . $resultSec . PHP_EOL;
    $json[] = ['count' => $itemsCount, 'sec' => $resultSec];
}
file_put_contents('result.json', json_encode($json));