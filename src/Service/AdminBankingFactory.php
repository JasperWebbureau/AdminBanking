<?php
declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Service;

use Flexgrid\Database\Connection;
use Flexgrid\Modules\AdminBanking\Application\UseCase\CreateBankAccount;
use Flexgrid\Modules\AdminBanking\Application\UseCase\ImportBankStatement;
use Flexgrid\Modules\AdminBanking\Application\UseCase\GenerateBankMatchProposals;
use Flexgrid\Modules\AdminBanking\Application\UseCase\GetBankTransactionMatches;
use Flexgrid\Modules\AdminBanking\Application\UseCase\AcceptBankMatch;
use Flexgrid\Modules\AdminBanking\Application\UseCase\AcceptManualBankMatch;
use Flexgrid\Modules\AdminBanking\Application\UseCase\IgnoreBankTransaction;
use Flexgrid\Modules\AdminBanking\Application\UseCase\ListBankAccounts;
use Flexgrid\Modules\AdminBanking\Application\UseCase\ListBankTransactions;
use Flexgrid\Modules\AdminBanking\Application\UseCase\SearchBankMatchTargets;
use Flexgrid\Modules\AdminBanking\Infrastructure\Import\AbnTabStatementParser;
use Flexgrid\Modules\AdminBanking\Infrastructure\Persistence\PdoBankAccountRepository;
use Flexgrid\Modules\AdminBanking\Infrastructure\Persistence\PdoBankImportRepository;
use Flexgrid\Modules\AdminBanking\Infrastructure\Persistence\PdoBankMatchProposalRepository;
use Flexgrid\Modules\AdminBanking\Infrastructure\Persistence\PdoBankTransactionListRepository;
use Flexgrid\Modules\AdminBanking\Infrastructure\Persistence\PdoBankTransactionRepository;
use Flexgrid\Modules\AdminCore\Context\TenantContext;
use Flexgrid\Modules\AdminCore\Infrastructure\PdoTransactionManager;
use Flexgrid\Modules\AdminCore\Infrastructure\UuidV4Generator;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
use Flexgrid\Utils\_Time;

final class AdminBankingFactory
{
    public static function createListTransactions():ListBankTransactions{$connection=self::connection();return new ListBankTransactions(self::tenant(),new PdoBankTransactionListRepository($connection),new PdoBankAccountRepository($connection));}
    public static function createListAccounts():ListBankAccounts{return new ListBankAccounts(self::tenant(),new PdoBankAccountRepository(self::connection()));}
    public static function createCreateAccount():CreateBankAccount{$connection=self::connection();return new CreateBankAccount(self::tenant(),new UuidV4Generator(),new PdoTransactionManager($connection),new PdoBankAccountRepository($connection),new _Time());}
    public static function createImportStatement():ImportBankStatement{$connection=self::connection();return new ImportBankStatement(self::tenant(),new UuidV4Generator(),new PdoTransactionManager($connection),new PdoBankAccountRepository($connection),new PdoBankImportRepository($connection),new PdoBankTransactionRepository($connection),new AbnTabStatementParser(),new _Time());}
    public static function createOverviewPresenter():BankingOverviewPresenter{return new BankingOverviewPresenter();}
    public static function createGetMatches():GetBankTransactionMatches{$connection=self::connection();return new GetBankTransactionMatches(self::tenant(),new PdoBankTransactionRepository($connection),new PdoBankMatchProposalRepository($connection));}
    public static function createGenerateMatches():GenerateBankMatchProposals{$connection=self::connection();$providers=(new BankMatchCandidateProviderLoader(dirname(__DIR__,3)))->getProviders();return new GenerateBankMatchProposals(self::tenant(),new UuidV4Generator(),new PdoTransactionManager($connection),new PdoBankTransactionRepository($connection),new PdoBankMatchProposalRepository($connection),$providers,new _Time());}
    public static function createAcceptMatch():AcceptBankMatch{$connection=self::connection();$processors=(new BankMatchProcessorLoader(dirname(__DIR__,3)))->getProcessors();return new AcceptBankMatch(self::tenant(),new PdoTransactionManager($connection),new PdoBankTransactionRepository($connection),new PdoBankMatchProposalRepository($connection),$processors,new _Time());}
    public static function createSearchTargets():SearchBankMatchTargets{$connection=self::connection();$providers=(new BankMatchTargetProviderLoader(dirname(__DIR__,3)))->getProviders();return new SearchBankMatchTargets(self::tenant(),new PdoBankTransactionRepository($connection),$providers);}
    public static function createAcceptManualMatch():AcceptManualBankMatch{$connection=self::connection();$providers=(new BankMatchTargetProviderLoader(dirname(__DIR__,3)))->getProviders();$processors=(new BankMatchProcessorLoader(dirname(__DIR__,3)))->getProcessors();return new AcceptManualBankMatch(self::tenant(),new PdoTransactionManager($connection),new PdoBankTransactionRepository($connection),$providers,$processors,new _Time());}
    public static function createIgnoreTransaction():IgnoreBankTransaction{$connection=self::connection();return new IgnoreBankTransaction(self::tenant(),new PdoTransactionManager($connection),new PdoBankTransactionRepository($connection),new _Time());}
    public static function createMatchPresenter():BankMatchPresenter{return new BankMatchPresenter();}
    private static function connection():\PDO{return Connection::getConnections();}
    private static function tenant():TenantContext{if(!defined('__ADMIN_TENANT_ID__')){throw new \LogicException('Definieer __ADMIN_TENANT_ID__ expliciet voor de Admin-modules.');}return new TenantContext(new TenantId((string)constant('__ADMIN_TENANT_ID__')));}
}
