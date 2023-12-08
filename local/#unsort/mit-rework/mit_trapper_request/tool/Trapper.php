<?php

namespace MIT\Tool;

class Trapper
{
  const CATCH_BEFORE = 1;

  private static $BufferLock = true;
  private static $Buffer = '';
  private static $BufferBeforeInit = '';
  private static \Closure $BufferHandler;
  private static object $BufferHandlerContext;

  private static ?Trapper $Inited = null;

  #region Single
  public static function InitOnce(int $catch = 0, ?int $flags = PHP_OUTPUT_HANDLER_STDFLAGS): Trapper
  {
    if (empty(self::$Inited))
      self::$Inited = new self($catch, $flags);
    else
      throw new \Exception("Trapper already creation", 1);

    return self::$Inited;
  }

  private static function _ClearTrapper(): void
  {
    self::$Inited = null;
  }
  #endregion

  private function __construct(int $catch, int $flags)
  {
    empty(self::$BufferHandler)
      && self::$BufferHandler = fn ($buffer) => $buffer;

    empty(self::$BufferHandlerContext)
      && self::$BufferHandlerContext = new class()
      {
      };

    $catch
      && self::$BufferBeforeInit = ob_get_contents();

    ob_start(
      fn ($buffer) => self::Jail($buffer),
      flags: $flags
    );
  }

  public function __destruct()
  {
    self::_BufferUnlock();
    self::Flush();
  }

  static public function Jail($buffer): string
  {
    self::$Buffer .= $buffer;

    if (self::_BufferLockState())
      return '';
    else
      return $buffer;
  }


  static public function Flush(): void
  {
    self::_ClearTrapper();

    ob_end_clean();

    echo self::JailCallbak();
  }

  #region Callback
  static public function SetJailCallbak(\Closure $Cl, ?object $Context = null)
  {
    self::$BufferHandler = $Cl;
    !empty($Context)
      && self::$BufferHandlerContext = $Context;
  }


  static public function JailCallbak()
  {
    return self::$BufferHandler->call(self::$BufferHandlerContext, self::$Buffer, self::$BufferBeforeInit);
  }
  #endregion

  #region BufferState
  private static function _BufferLockState(): bool
  {
    return self::$BufferLock;
  }

  private static function _BufferUnlock(): void
  {
    self::$BufferLock = false;
  }
  #endregion
}
