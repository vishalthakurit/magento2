<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Blog\Controller\Adminhtml\Topic;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Mageplaza\Blog\Controller\Adminhtml\Topic;
use Mageplaza\Blog\Model\TopicFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 * @package Mageplaza\Blog\Controller\Adminhtml\Topic
 */
class Save extends Topic
{
    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    public $jsHelper;

    /**
     * Layout Factory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * Result Json Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Js $jsHelper
     * @param LayoutFactory $layoutFactory
     * @param JsonFactory $resultJsonFactory
     * @param TopicFactory $topicFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory,
        TopicFactory $topicFactory
    ) {
        $this->jsHelper = $jsHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $registry, $topicFactory);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $topic = $this->initTopic();
            $topicPostData = $this->getRequest()->getPostValue();
            $topicPostData['store_ids'] = 0;
            $topicPostData['enabled'] = 1;

            $topic->addData($topicPostData);

            try {
                $topic->save();
                $this->messageManager->addSuccess(__('You saved the topic.'));
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving the topic.'));
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            }

            $hasError = (bool) $this->messageManager->getMessages()->getCountByType(
                MessageInterface::TYPE_ERROR
            );

            $topic->load($topic->getId());
            $topic->addData([
                'level'     => 1,
                'entity_id' => $topic->getId(),
                'is_active' => $topic->getEnabled(),
                'parent'    => 0
            ]);

            // to obtain truncated category name
            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            return $resultJson->setData(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error'    => $hasError,
                    'category' => $topic->toArray(),
                ]
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('topic')) {
            /** @var \Mageplaza\Blog\Model\Topic $topic */
            $topic = $this->initTopic();
            $topic->setData($data);

            if ($posts = $this->getRequest()->getPost('posts', false)) {
                $topic->setPostsData($this->jsHelper->decodeGridSerializedInput($posts));
            }

            try {
                $topic->save();

                $this->messageManager->addSuccess(__('The Topic has been saved.'));
                $this->_getSession()->setData('mageplaza_blog_topic_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mageplaza_blog/*/edit', ['id' => $topic->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mageplaza_blog/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Topic.'));
            }

            $this->_getSession()->setData('mageplaza_blog_topic_data', $data);

            $resultRedirect->setPath('mageplaza_blog/*/edit', ['id' => $topic->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mageplaza_blog/*/');

        return $resultRedirect;
    }
}
