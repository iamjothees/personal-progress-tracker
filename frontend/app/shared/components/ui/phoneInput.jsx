import { Input } from '@/components/ui/input';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';

export function PhoneInput({ control, countryCodeName, phoneNumberName }) {
  return (
    <div className="grid grid-cols-3 gap-4">
      <FormField
        control={control}
        name={countryCodeName}
        render={({ field }) => (
          <FormItem className="col-span-1">
            <FormLabel>Code</FormLabel>
            <FormControl>
              <Input placeholder="+1" {...field} autoComplete="tel-country-code" />
            </FormControl>
            <FormMessage />
          </FormItem>
        )}
      />
      <FormField
        control={control}
        name={phoneNumberName}
        render={({ field }) => (
          <FormItem className="col-span-2">
            <FormLabel>Phone Number</FormLabel>
            <FormControl>
              <Input placeholder="123-456-7890" {...field} autoComplete="tel-national" />
            </FormControl>
            <FormMessage />
          </FormItem>
        )}
      />
    </div>
  );
}
