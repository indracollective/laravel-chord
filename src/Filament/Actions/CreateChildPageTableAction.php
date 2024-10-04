<?php

namespace LiveSource\Chord\Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Filament\Resources\PageResource;

class CreateChildPageTableAction extends Action
{
    use CanCustomizeProcess;

    protected bool | Closure $canCreateAnother = true;

    protected ?Closure $getRelationshipUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'create';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->icon('heroicon-o-plus-circle')
            ->modalWidth('md')
            ->form(fn () => PageResource::getSettingsFormSchema($this))
            ->label('Add Child Page')
            ->fillForm(function (array $data) {
                $data['type'] = Chord::getDefaultPageType();
                $data['parent_id'] = $this->record->id;

                return $data;
            })
            ->mutateFormDataUsing(function (array $data): array {
                $data['is_published'] = false;

                return $data;
            });

        $this->modalHeading(fn (): string => __('filament-actions::create.single.modal.heading', ['label' => $this->getModelLabel()]));
        $this->modalSubmitActionLabel(__('filament-actions::create.single.modal.actions.create.label'));
        $this->extraModalFooterActions(function (): array {
            return $this->canCreateAnother() ? [
                $this->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label(__('filament-actions::create.single.modal.actions.create_another.label')),
            ] : [];
        });

        $this->successNotificationTitle(__('filament-actions::create.single.notifications.created.title'));

        $this->action(function (array $arguments, Form $form): void {
            $model = $this->getModel();

            $record = $this->process(function (array $data, HasActions $livewire) use ($model): Model {
                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $record = $translatableContentDriver->makeRecord($model, $data);
                } else {
                    $record = new $model;
                    $record->fill($data);
                }

                if ($relationship = $this->getRelationship()) {
                    /** @phpstan-ignore-next-line */
                    $relationship->save($record);

                    return $record;
                }

                $record->save();

                return $record;
            });

            $this->record($record);
            $form->model($record)->saveRelationships();

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $form->model($model);

                $form->fill();

                $this->halt();

                return;
            }

            $this->success();
        });
    }

    public function relationship(?Closure $relationship): static
    {
        $this->getRelationshipUsing = $relationship;

        return $this;
    }

    public function createAnother(bool | Closure $condition = true): static
    {
        $this->canCreateAnother = $condition;

        return $this;
    }

    /**
     * @deprecated Use `createAnother()` instead.
     */
    public function disableCreateAnother(bool | Closure $condition = true): static
    {
        $this->createAnother(fn (CreateChildPageTableAction $action): bool => ! $action->evaluate($condition));

        return $this;
    }

    public function canCreateAnother(): bool
    {
        return (bool) $this->evaluate($this->canCreateAnother);
    }

    public function shouldClearRecordAfter(): bool
    {
        return true;
    }

    public function getRelationship(): Relation | Builder | null
    {
        return $this->evaluate($this->getRelationshipUsing);
    }
}
