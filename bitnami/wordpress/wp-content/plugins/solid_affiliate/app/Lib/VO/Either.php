<?php

namespace SolidAffiliate\Lib\VO;

/**
 * @template T
 * 
 * @psalm-type Left = array<string>
 * @psalm-type Right = mixed
 * 
 */
class Either
{

    /** @var array<string> */
    public $left;

    /** @var T */
    public $right;

    /** @var bool */
    public $isRight;

    /** @var bool */
    public $isLeft;

    /**
     * @param array<string> $left
     * @param T $right
     * @param bool $isRight
     */
    public function __construct($left, $right, $isRight)
    {
        $this->left = $left;
        $this->right = $right;
        $this->isRight = $isRight;
        $this->isLeft = !$isRight;
    }


    /**
     * NOTE This does not work. Type signature of the Either param
     * can't be passed in Generically to the $right_handler function param
     * type signature.
     * 
     * @template T0 of Either
     *
     * @param T0 $either
     * @param Closure(Left):mixed $left_handler
     * @param Closure(T0):mixed $right_handler
     * 
     * @return mixed
     */
    // public static function handle($either, $left_handler, $right_handler)
    // {
    //     if ($either->isLeft) {
    //         return $left_handler($either->left);
    //     } {
    //         return $right_handler($either);
    //     }
    // }
}
