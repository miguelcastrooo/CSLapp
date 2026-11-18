import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';

export const authGuard: CanActivateFn = () => sessionStorage.getItem('csl_api_token') ? true : inject(Router).createUrlTree(['/login']);
