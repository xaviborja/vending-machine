<?php

declare(strict_types=1);

namespace App\Ui\Command;

use App\Domain\VendingMachine\Coin\Coin;
use App\Domain\VendingMachine\Coin\InvalidCoinException;
use App\Domain\VendingMachine\VendingMachine\ItemNotAvailableException;
use App\Domain\VendingMachine\VendingMachine\NotEnoughMoneyForItemException;
use App\Domain\VendingMachine\VendingMachine\VendingMachine;
use App\Domain\VendingMachine\Wallet\NotEnoughChangeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class InitializeVendingMachine extends Command
{
    private const SET_AVAILABLE_CHANGE_QUESTION = 'Set available change';
    private const SET_ITEMS_QUANTITY_QUESTION = 'Set items quantity';
    private const INSERT_COIN_QUESTION = 'Insert Coin';
    private const SELECT_ITEM_QUESTION = 'Select item';
    private const RETURN_COINS_INSERTED_QUESTION = 'Return coins inserted';
    private const SERVICE_QUESTION = 'Service';
    private const EXIT_QUESTION = 'Exit';

    protected static $defaultName = 'app:initialize-vending-machine';

    private QuestionHelper $questionHelper;
    private VendingMachine $vendingMachine;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->vendingMachine = $this->initializeVendingMachine();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->questionHelper = $this->getHelper('question');
        $output->writeln('Welcome to Vending Machine!');
        do {
            $question = new ChoiceQuestion(
                'What do you want to do?',
                [
                    self::INSERT_COIN_QUESTION, 
                    self::SELECT_ITEM_QUESTION, 
                    self::RETURN_COINS_INSERTED_QUESTION,
                    self::SERVICE_QUESTION,
                    self::EXIT_QUESTION
                ],
                0
            );
            $question->setErrorMessage('Selection %s is invalid.');
            $response = $this->questionHelper->ask($input, $output, $question);
            if ($response === self::INSERT_COIN_QUESTION) {
                $this->insertCoin($input, $output);
            }
            if ($response === self::SELECT_ITEM_QUESTION) {
                $this->selectItem($input, $output);
            }
            if ($response === self::RETURN_COINS_INSERTED_QUESTION) {
                $output->writeln('<fg=green>' . implode(',', $this->vendingMachine->returnCoins()) . '</>');
            }
            if ($response === self::SERVICE_QUESTION) {
                $this->service($input, $output);
            }
        } while($response !== self::EXIT_QUESTION);

        return Command::SUCCESS;
    }

    protected function insertCoin(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $question = new Question('Please insert coin' . PHP_EOL);
        $coin = $this->questionHelper->ask($input, $output, $question);
        try {
            $this->vendingMachine->insertCoin(new Coin((float)$coin));
        } catch (InvalidCoinException $e) {
            $output->writeln('<fg=red>Invalid coin.</>');
        }
    }

    protected function selectItem(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $items = [0 => 'Water: 0.65', 1 => 'Juice: 1', 2 => 'Soda: 1.50'];
        $question = new ChoiceQuestion(
            'Choose an item',
            $items
        );
        $question->setErrorMessage('Selection %s is invalid.');
        $response = $this->questionHelper->ask($input, $output, $question);
        try {
            $key = array_search($response, $items, true);
            $itemSold = $this->vendingMachine->select($key);
            $output->writeln($itemSold->name());
            $output->writeln('<fg=green>' . $itemSold->name() . '</>');
            if ($itemSold->change()->totalAmount() > 0) {
                $output->writeln('<fg=green>' . implode(',', $itemSold->change()->toArray()) . '</>');
            }
        } catch (NotEnoughChangeException $e) {
            $output->writeln('<fg=red>I am sorry, I do not have enough change.</>');
        }
        catch (NotEnoughMoneyForItemException $e) {
            $output->writeln('<fg=red>I am sorry, you do not have enough money</>');
        }
        catch (ItemNotAvailableException $e) {
            $output->writeln('<fg=red>I am sorry, this item is not available</>');
        }

    }

    protected function initializeVendingMachine(): VendingMachine
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Water', 0.65, 10, 0);
        $vendingMachine->add('Juice', 1, 10, 1);
        $vendingMachine->add('Soda', 1.50, 10, 2);
        return $vendingMachine;
    }

    private function service(InputInterface  $input, OutputInterface $output): void
    {
        $question = new ChoiceQuestion(
            'Service: What do you want to do?',
            [self::SET_AVAILABLE_CHANGE_QUESTION, self::SET_ITEMS_QUANTITY_QUESTION]
        );
        $question->setErrorMessage('Selection %s is invalid.');
        switch ($this->questionHelper->ask($input, $output, $question)) {
            case self::SET_AVAILABLE_CHANGE_QUESTION:
                $question = new Question('Insert coin' . PHP_EOL);
                $coin = $this->questionHelper->ask($input, $output, $question);
                $this->vendingMachine->addCoinForChange(new Coin((float)$coin), 1);
                break;
            case self::SET_ITEMS_QUANTITY_QUESTION:
                $question = new Question('Choose item by selector' . PHP_EOL);
                $selector = $this->questionHelper->ask($input, $output, $question);
                $question = new Question('Set quantity' . PHP_EOL);
                $quantity = $this->questionHelper->ask($input, $output, $question);
                $this->vendingMachine->updateItemQuantityBySelector((int)$selector, (int)$quantity);
                break;
        }
    }
}
