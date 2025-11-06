import { useState, useEffect } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSeparator,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import AuthLayout from '@/layouts/auth-layout';
import SendOtpController from '@/actions/App/Http/Controllers/Auth/SendOtpController';
import VerifyOtpController from '@/actions/App/Http/Controllers/Auth/VerifyOtpController';
import { Form, Head, usePage } from '@inertiajs/react';
import { toast } from 'sonner';

interface LoginProps {
    otpSent?: boolean;
    email?: string;
}

export default function Login({ otpSent: initialOtpSent, email: initialEmail }: LoginProps) {
    const [otpSent, setOtpSent] = useState(initialOtpSent || false);
    const [email, setEmail] = useState(initialEmail || '');
    const [otp, setOtp] = useState('');
    const [timeLeft, setTimeLeft] = useState(300);

    const { props } = usePage<{ otpSent?: boolean; email?: string }>();

    useEffect(() => {
        if (props.otpSent) {
            setOtpSent(true);
            setEmail(props.email || '');
            setTimeLeft(300);
        }
    }, [props.otpSent, props.email]);

    useEffect(() => {
        if (!otpSent || timeLeft <= 0) {
            return;
        }

        const timer = setInterval(() => {
            setTimeLeft((prev) => {
                if (prev <= 1) {
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(timer);
    }, [otpSent, timeLeft]);

    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;

    return (
        <AuthLayout
            title={otpSent ? 'Enter verification code' : 'Log in to your account'}
            description={
                otpSent
                    ? 'Enter the code sent to your email'
                    : 'Enter your email to receive a login code'
            }
        >
            <Head title="Log in" />

            {!otpSent ? (
                <Form
                    {...SendOtpController.form()}
                    onSuccess={() => {
                        setOtpSent(true);
                        setTimeLeft(300);
                    }}
                    onError={(errors) => {
                        if (errors.message) {
                            toast.error(errors.message);
                        }
                    }}
                    className="flex flex-col gap-6"
                >
                    {({ processing, errors, data }) => (
                        <div className="grid gap-2">
                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    autoComplete="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <Button
                                type="submit"
                                className="mt-4 w-full"
                                disabled={processing}
                                data-test="send-code-button"
                            >
                                {processing && <Spinner />}
                                Send code
                            </Button>
                        </div>
                    )}
                </Form>
            ) : (
                <Form
                    {...VerifyOtpController.form()}
                    transform={(data) => ({ ...data, email, otp })}
                    onSuccess={() => {
                        window.location.href = '/dashboard';
                    }}
                    onError={(errors) => {
                        if (errors.otp) {
                            toast.error(errors.otp);
                        }
                        if (errors.message) {
                            toast.error(errors.message);
                        }
                    }}
                    className="flex flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <input type="hidden" name="email" value={email} />
                                <div className="grid gap-2">
                                    <Label htmlFor="otp">Verification code</Label>
                                    <InputOTP
                                        maxLength={6}
                                        value={otp}
                                        onChange={setOtp}
                                        name="otp"
                                    >
                                        <InputOTPGroup>
                                            <InputOTPSlot index={0} />
                                            <InputOTPSlot index={1} />
                                            <InputOTPSlot index={2} />
                                        </InputOTPGroup>
                                        <InputOTPSeparator />
                                        <InputOTPGroup>
                                            <InputOTPSlot index={3} />
                                            <InputOTPSlot index={4} />
                                            <InputOTPSlot index={5} />
                                        </InputOTPGroup>
                                    </InputOTP>
                                    <input type="hidden" name="otp" value={otp} />
                                </div>

                                {timeLeft > 0 && (
                                    <p className="text-left text-sm text-muted-foreground">
                                        Code expires in {timeString}
                                    </p>
                                )}

                                {timeLeft === 0 && (
                                    <p className="text-left text-sm text-red-600 dark:text-red-400">
                                        Code expired. Please request a new one.
                                    </p>
                                )}

                                <Button
                                    type="submit"
                                    className="mt-4 w-full"
                                    disabled={processing || timeLeft === 0}
                                    data-test="verify-code-button"
                                >
                                    {processing && <Spinner />}
                                    Verify code
                                </Button>
                            </div>

                            <div className="text-center text-sm text-muted-foreground">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setOtpSent(false);
                                        setOtp('');
                                        setTimeLeft(300);
                                    }}
                                    className="text-primary hover:underline"
                                >
                                    Use a different email
                                </button>
                            </div>
                        </>
                    )}
                </Form>
            )}
        </AuthLayout>
    );
}
