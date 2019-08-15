import {Component, Input, OnInit, Output, EventEmitter} from '@angular/core';
import {QuoteService} from "@app/home/quote.service";
import {finalize} from "rxjs/operators";
import {Question} from "@app/test/models/question";
import {Answer} from "@app/test/models/answer";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'question',
  template: `
    <h2>{{question.question}}</h2>
    
    <!--
    <mat-step label="A" [stepControl]="question.id">
      <div fxLayout="column" style="width: 80%; margin:0 auto;">
        <div fxLayout="column" fxFlex="100%" fxLayoutGap="2em">
-->
          <mat-radio-group fxLayout="column"
                           fxLayoutGap="1em">
            <mat-radio-button *ngFor="let answer of question.answers; index as i"
                              [value]="i"
                              (change)="radioChange(answer)">
              <span innerHTML="{{answer.answer}}"></span>
            </mat-radio-button>
          </mat-radio-group>

    <!--
      </div>
    </div>
  </mat-step>
  -->
 
        <!--
        <inno-survey-question (goNext)="moveNextQuestion($event)" [instantNext]="true" [order]="question.order" [formGroup]="formGroups[i+1]"></inno-survey-question>
        -->
    <!--[formControlName]="question.id"-->

  `,
  styleUrls: ['./question.component.scss']
})
export class QuestionComponent implements OnInit {
  @Input() question:Question;
  @Output() nextStep = new EventEmitter();


  quote: string | undefined;
  isLoading = false;

  constructor(private quoteService: QuoteService,
              private toastr: ToastrService) {}

  ngOnInit() {

    this.isLoading = true;
    this.quoteService
      .getRandomQuote({ category: 'dev' })
      .pipe(
        finalize(() => {
          this.isLoading = false;
        })
      )
      .subscribe((quote: string) => {
        this.quote = quote;
      });
  }

  radioChange(answer: Answer){
    console.log("MOVE TO NEXT QUESTION");
    console.log(answer);
    if (answer.correct) {
      this.toastr.success('Respuesta', 'Correcta!');
      this.nextStep.emit();
    }else{
      this.toastr.error('Respuesta', 'Incorrecta!');
    }
  }

}
