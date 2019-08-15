import {Component, OnInit, ViewChildren} from '@angular/core';
import { finalize } from 'rxjs/operators';

import { QuoteService } from './quote.service';
import {FormBuilder, FormControl, FormGroup, Validators} from "@angular/forms";
import {MatStepper} from "@angular/material";
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {Question} from "@app/test/models/question";

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  @ViewChildren('stepper') stepper: any;

  formGroups: Array<FormGroup> = [];
  form: FormGroup;

  quote: string | undefined;
  isLoading = false;

  tests: any = [];
  questions$: Observable<Question>;
  questions = [
    {id: 1, question: "Question 1", answers: [{id: 1, answer: "Answer a", correct: false}, {id: 1, answer: "Answer b", correct: false}, {id: 1, answer: "Answer c", correct: true}, {id: 1, answer: "Answer d", correct: false}]},
    {id: 2, question: "Question 2", answers: [{id: 1, answer: "Answer a", correct: false}, {id: 1, answer: "Answer b", correct: false}, {id: 1, answer: "Answer c", correct: false}, {id: 1, answer: "Answer d", correct: true}]},
  ];

  constructor(private httpClient: HttpClient,
              private quoteService: QuoteService,
              private fb: FormBuilder) {}

  ngOnInit() {
    this.getTests().subscribe(tests => {
      console.log(tests);
      this.tests = tests;
    });

    for (let i = 0; i < this.questions.length; i++) {
      const group = this.fb.group({});
      group.addControl(''+i, new FormControl(i, Validators.required));
      this.formGroups.push(group);
    }


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

  public getTests(){
    return this.httpClient.get(`http://localhost:8000/api.php`);
  }

  public getTest(id: number){
    this.questions$ = this.httpClient.get(`http://localhost:8000/api.php?id=`+id);
  }

  moveNext(stepper: MatStepper){
    stepper.next();
  }

  startTest(id: number){
    this.getTest(id);
  }
}
