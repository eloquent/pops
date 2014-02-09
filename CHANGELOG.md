# Pops changelog

## 4.1.0 (2014-02-09)

- **[FIXED]** Added accessor methods for important private class members
  required by subclasses (value, isRecursive etc.) - in version 3 these were
  protected and so 'just worked'.
- **[FIXED]** Proxies now actually implement `ProxyInterface` (lol).
- **[NEW]** Added formal interfaces for each proxy type.
- **[NEW]** Added `ProxyInterface::setPopsValue()` and
    `ProxyInterface::popsValue()`.
- **[IMPROVED]** Deprecated methods in favour of `ProxyInterface::popsValue()`:
    - `ProxyArray::popsArray()`
    - `ProxyClass::popsClass()`
    - `ProxyObject::popsObject()`
    - `ProxyPrimitive::popsPrimitive()`
- **[IMPROVED]** Refactored some common code into abstract base classes.
- **[MAINTENANCE]** Minor repo maintenance

## 4.0.1 (2014-01-30)

- **[MAINTENANCE]** General repository maintanance

## 4.0.0 (2013-09-04)

- **[BC BREAK]** Interface name changes:
    - `Proxy` -> `ProxyInterface`
    - `Safe` -> `SafeInterface`
- **[BC BREAK]** `Pops` class renamed to `Proxy`
- **[NEW]** API documentation
- **[MAINTENANCE]** Major repository maintanance

## 3.1.1 (2013-03-04)

- **[NEW]** [Archer](https://github.com/IcecaveStudios/archer) integration
- **[NEW]** Implemented changelog
