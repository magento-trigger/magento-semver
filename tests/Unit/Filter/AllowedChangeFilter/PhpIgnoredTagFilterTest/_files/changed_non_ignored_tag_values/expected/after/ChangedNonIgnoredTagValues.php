<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SemanticVersionCheckr\Test\Unit\Filter\AllowedChangeFilter\PhpIgnoredTagFilterTest;

class ChangedNonIgnoredTagValues
{
    /**
     * This tag is one whose values are not ignored and thus needs to be compared
     * After changing the tag values, the files do not match
     * @nonignoredvals After Val
     */
    public function foo()
    {
        return;
    }
}
